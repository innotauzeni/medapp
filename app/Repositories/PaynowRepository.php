<?php

namespace App\Repositories;

use App\Models\BookPayment;
use App\Models\Booking;
use App\Models\PaynowTransaction;
use App\Repositories\Contracts\PaynowRepositoryInterface;
use App\Repositories\Contracts\PaynowTransactionRepositoryInterface;
use App\Repositories\Contracts\PaymentChannelRepositoryInterface;
use Illuminate\Support\Str;
use Paynow\Payments\Paynow;

class PaynowRepository extends BaseRepository implements PaynowRepositoryInterface
{
    protected $paynowtransaction;

    public function __construct(
        PaynowTransactionRepositoryInterface $paynowtransaction,
        Booking $booking,
        private readonly PaymentChannelRepositoryInterface $paymentchannels,
    ) {
        parent::__construct($booking);
        $this->paynowtransaction = $paynowtransaction;
    }

    /** Build a Paynow client using channel parameters stored in DB. */
    private function makePaynow(string $integrationId, string $integrationKey, string $uuid): Paynow
    {
        $returnUrl = route('public.paynow.check', ['uuid' => $uuid]);
        $resultUrl = route('public.paynow.check', ['uuid' => $uuid]);
        return new Paynow($integrationId, $integrationKey, $returnUrl, $resultUrl);
    }

    /** Resolve credentials: DB parameters first, fall back to config. */
    private function resolveCredentials(\App\Models\PaymentChannel $channel): array
    {
        $params = $channel->parameters ?? collect();
        $id     = $params->where('key', 'IntegrationId')->first()?->value
                  ?: config('paynowconfig.integration_id', '');
        $key    = $params->where('key', 'Integrationkey')->first()?->value
                  ?: config('paynowconfig.integration_key', '');
        return [$id, $key];
    }

    public function initiatetransaction(array $data): array
    {
        try {
            // Fresh query with eager loaded items — works correctly even when called
            // from inside a DB transaction because items are already committed at this point
            $booking = \App\Models\Booking::with(['items.course'])->find($data['booking_id']);
            if (!$booking) {
                return ['status' => 'error', 'message' => 'Booking not found'];
            }

            $channel = $this->paymentchannels->query()
                ->with('parameters')
                ->where('slug', 'paynow')
                ->where('is_active', true)
                ->first();

            if (!$channel) {
                return ['status' => 'error', 'message' => 'Paynow payment channel not configured or inactive'];
            }

            [$integrationId, $integrationKey] = $this->resolveCredentials($channel);

            if (empty($integrationId) || empty($integrationKey)) {
                return ['status' => 'error', 'message' => 'Paynow integration credentials are missing. Please contact the administrator.'];
            }

            $uuid   = (string) ($data['uuid'] ?? Str::uuid()->toString());
            $paynow = $this->makePaynow($integrationId, $integrationKey, $uuid);

            // In test mode, Paynow requires authemail to match the merchant's registered email.
            // In production use the customer's actual email.
            $isTestMode = strtolower(config('paynowconfig.mode', 'test')) === 'test';
            $authEmail  = $isTestMode
                ? config('paynowconfig.merchant_email', $booking->email)
                : ($booking->email ?: ($data['email'] ?? ''));

            $payment = $paynow->createPayment($uuid, $authEmail);

            // Add line items — prefer eager-loaded items, fall back to amount from $data
            $itemsAdded = 0;
            foreach ($booking->items as $item) {
                $label  = $item->course?->title ?? 'Course';
                $amount = round((float) $item->unit_price * (int) $item->quantity, 2);
                if ($amount > 0) {
                    $payment->add($label, $amount);
                    $itemsAdded++;
                }
            }

            // Final fallback — if still no items (e.g. fee is 0), add total from $data
            if ($itemsAdded === 0) {
                $fallbackAmount = round((float) ($data['amount'] ?? 0), 2);
                if ($fallbackAmount <= 0) {
                    return ['status' => 'error', 'message' => 'Booking total is zero — cannot initiate Paynow payment.'];
                }
                $payment->add('Booking ' . $booking->booking_code, $fallbackAmount);
            }

            // Suppress E_DEPRECATED from utf8_encode() inside the Paynow SDK
            // (deprecated in PHP 8.2, removed in 8.4 — SDK uses it internally).
            // We patch only our call scope; the SDK is otherwise unmodified.
            $prev = error_reporting(error_reporting() & ~E_DEPRECATED);
            $response = $paynow->send($payment);
            error_reporting($prev);

            if ($response->success()) {
                $link    = $response->redirectUrl();
                $pollurl = $response->pollUrl();

                $transaction = $this->paynowtransaction->create([
                    'booking_id'         => $booking->id,
                    'payment_channel_id' => $channel->id,
                    'book_payment_id'    => $data['book_payment_id'] ?? null,
                    'uuid'               => $uuid,
                    'pollurl'            => $pollurl,
                    'amount'             => $data['amount'] ?? 0,
                    'currency'           => $data['currency'] ?? currency_code(),
                    'status'             => 'PENDING',
                ]);

                if (!empty($data['book_payment_id'])) {
                    BookPayment::query()->where('id', $data['book_payment_id'])->update([
                        'pollurl' => $pollurl,
                        'uuid'    => $uuid,
                        'status'  => 'PENDING',
                    ]);
                }

                return [
                    'status'         => 'success',
                    'message'        => 'Payment initiated. Redirecting to Paynow…',
                    'redirecturl'    => $link,
                    'pollurl'        => $pollurl,
                    'uuid'           => $uuid,
                    'transaction_id' => $transaction->id,
                ];
            }

            // Surface the actual Paynow error
            $responseData = $response->data();
            $paynowError  = $responseData['error'] ?? ($responseData['status'] ?? 'unknown error');

            \Illuminate\Support\Facades\Log::error('Paynow initiation failed', [
                'booking_id'    => $booking->id,
                'response_data' => $responseData,
                'integration_id'=> $integrationId,
            ]);

            return ['status' => 'error', 'message' => 'Paynow error: ' . $paynowError];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Paynow exception in initiatetransaction', [
                'booking_id' => $data['booking_id'] ?? null,
                'error'      => $e->getMessage(),
                'class'      => get_class($e),
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function gettransactions(int $booking_id): \Illuminate\Database\Eloquent\Collection
    {
        return $this->paynowtransaction->query()
            ->with('booking', 'paymentChannel', 'bookPayment')
            ->where('booking_id', $booking_id)
            ->latest('id')
            ->get();
    }

    public function checktransaction(string $uuid): array
    {
        $transaction = $this->paynowtransaction->query()
            ->with('booking', 'paymentChannel.parameters', 'bookPayment')
            ->where('uuid', $uuid)
            ->first();

        if (!$transaction) {
            return ['status' => 'error', 'message' => 'Transaction not found'];
        }

        return $this->pollAndUpdate($transaction);
    }

    public function checktransactionbyid(int $id): array
    {
        $transaction = $this->paynowtransaction->query()
            ->with('booking', 'paymentChannel.parameters', 'bookPayment')
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            return ['status' => 'error', 'message' => 'Transaction not found'];
        }

        return $this->pollAndUpdate($transaction);
    }

    private function pollAndUpdate(PaynowTransaction $transaction): array
    {
        try {
            [$integrationId, $integrationKey] = $this->resolveCredentials($transaction->paymentChannel);

            if (empty($integrationId) || empty($integrationKey)) {
                return ['status' => 'error', 'message' => 'Paynow credentials missing'];
            }

            $paynow   = $this->makePaynow($integrationId, $integrationKey, $transaction->uuid);
            $response = $paynow->pollTransaction($transaction->pollurl);

            if ($response->paid()) {
                $transaction->update(['status' => 'PAID']);
                $transaction->bookPayment?->update(['status' => 'PAID']);
                return ['status' => 'success', 'message' => 'Payment confirmed — transaction paid.'];
            }

            $status = strtoupper((string) $response->status());
            $transaction->update(['status' => $status]);
            $transaction->bookPayment?->update(['status' => $status]);

            return ['status' => 'pending', 'message' => 'Payment status: ' . $status];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
