<?php

namespace App\Services;

use App\Models\BookPayment;
use App\Models\Booking;
use App\Repositories\Contracts\BookPaymentRepositoryInterface;
use App\Repositories\Contracts\PaymentChannelRepositoryInterface;
use App\Repositories\PaynowRepository;
use App\Services\BookReceiptService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookPaymentService
{
    public function __construct(
        private readonly BookPaymentRepositoryInterface $payments,
        private readonly PaymentChannelRepositoryInterface $channels,
    ) {}

    public function createPayment(Booking $booking, int $paymentChannelId, string $status = 'PENDING'): BookPayment
    {
        return DB::transaction(function () use ($booking, $paymentChannelId, $status) {
            $paymentChannel = $this->channels->query()
                ->where('id', $paymentChannelId)
                ->first();

            if (!$paymentChannel) {
                throw new \RuntimeException('Payment channel not configured');
            }

            return $this->payments->create([
                'booking_id' => $booking->id,
                'payment_channel_id' => $paymentChannel->id,
                'total_ammount' => (float) $booking->total_amount,
                'uuid' => $paymentChannel->slug === 'paynow' ? Str::uuid()->toString() : null,
                'currency' => currency_code(),
                'description' => 'Booking payment for ' . $booking->booking_code,
                'status' => $status,
            ]);
        });
    }

    public function startPayment(Booking $booking, int $paymentChannelId): array
    {
        $paymentChannel = $this->channels->query()
            ->where('id', $paymentChannelId)
            ->first();

        if (!$paymentChannel) {
            return ['status' => 'error', 'message' => 'Payment channel not configured'];
        }

        if (!$paymentChannel->is_active) {
            return ['status' => 'error', 'message' => $paymentChannel->name . ' is not available'];
        }

        $payment = $this->createPayment($booking, $paymentChannel->id);

        if ($paymentChannel->slug === 'paynow') {
            $paynowRepo = app(PaynowRepository::class);
            $result = $paynowRepo->initiatetransaction([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'currency' => $payment->currency,
                'uuid' => $payment->uuid,
                'book_payment_id' => $payment->id,
            ]);

            if ($result['status'] !== 'success') {
                $payment->update(['status' => 'FAILED']);
                return $result;
            }

            if (!empty($result['pollurl'])) {
                $payment->update(['pollurl' => $result['pollurl']]);
            }

            $result['payment_id'] = $payment->id;
            $result['uuid'] = $payment->uuid;

            return $result;
        }

        if ($paymentChannel->slug === 'manual') {
            return [
                'status' => 'success',
                'message' => 'Manual payment recorded. Admin verification required.',
                'payment_id' => $payment->id,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Unsupported payment channel',
        ];
    }

    public function initPaynowPayment(Booking $booking): array
    {
        $paymentChannel = $this->channels->findByName('PAYNOW');

        if (!$paymentChannel) {
            return ['status' => 'error', 'message' => 'PAYNOW payment channel not configured'];
        }

        return $this->startPayment($booking, $paymentChannel->id);
    }

    public function checkPayment(BookPayment $payment): array
    {
        $payment->loadMissing(['paymentChannel']);

        if (!$payment->paymentChannel) {
            return ['status' => 'error', 'message' => 'Payment channel not configured'];
        }

        $result = match ($payment->paymentChannel->slug) {
            'paynow' => $this->checkPaynow($payment),
            'manual' => $this->checkManual($payment),
            default  => ['status' => 'error', 'message' => 'Unknown payment channel'],
        };

        // Auto-generate receipt when payment is confirmed PAID
        if ($result['status'] === 'success' && strtoupper($payment->fresh()->status) === 'PAID') {
            try {
                $receipt = app(BookReceiptService::class)->generateForPayment($payment->fresh());
                $result['receipt_id']     = $receipt->id;
                $result['receipt_number'] = $receipt->receipt_number;
                $result['message']       .= " Receipt {$receipt->receipt_number} generated.";
            } catch (\Throwable $e) {
                // Non-fatal — payment is still confirmed
                \Illuminate\Support\Facades\Log::warning('Receipt generation failed', [
                    'payment_id' => $payment->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    private function checkPaynow(BookPayment $payment): array
    {
        if (!$payment->uuid) {
            return ['status' => 'error', 'message' => 'Paynow transaction reference is missing'];
        }
        $paynowRepo = app(PaynowRepository::class);
        return $paynowRepo->checktransaction($payment->uuid);
    }

    private function checkManual(BookPayment $payment): array
    {
        $payment->update(['status' => 'PAID']);
        return ['status' => 'success', 'message' => 'Manual payment marked as paid.'];
    }

    public function checkPaymentByUuid(string $uuid): array
    {
        $payment = $this->payments->query()
            ->with(['paymentChannel'])
            ->where('uuid', $uuid)
            ->first();

        if (!$payment) {
            return ['status' => 'error', 'message' => 'Transaction not found'];
        }

        return $this->checkPayment($payment);
    }

    public function getPayment(int $id): ?BookPayment
    {
        return $this->payments->find($id);
    }

    public function getPaymentsForBooking(int $bookingId): array
    {
        return $this->payments->query()
            ->with(['paymentChannel'])
            ->where('booking_id', $bookingId)
            ->latest()
            ->get()
            ->toArray();
    }
}
