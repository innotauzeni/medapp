<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use App\Models\PaynowTransaction;
use App\Services\BookingService;
use App\Services\BookPaymentService;
use App\Services\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
        private readonly CartService $cart,
        private readonly BookPaymentService $payments,
    ) {}

    public function showForm(): View|RedirectResponse
    {
        $items = $this->cart->detailed();
        if (empty($items)) {
            return redirect()->route('public.cart')->with('error', 'Add a course to your cart first.');
        }

        $paymentChannels = PaymentChannel::where('is_active', true)->get(['id', 'name', 'slug']);

        return view('public.book', [
            'items' => $items,
            'total' => $this->cart->total(),
            'paymentChannels' => $paymentChannels,
        ]);
    }

    public function store(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'first_name'      => ['required', 'string', 'max:80'],
            'last_name'       => ['required', 'string', 'max:80'],
            'email'           => ['required', 'email', 'max:150'],
            'phone'           => ['required', 'string', 'max:30'],
            'id_type'         => ['required', 'in:id,passport'],
            'id_number'       => ['nullable', 'string', 'max:50'],
            'organization'    => ['nullable', 'string', 'max:150'],
            'city'            => ['nullable', 'string', 'max:100'],
            'notes'           => ['nullable', 'string', 'max:1500'],
            'payment_channel' => ['required', 'exists:payment_channels,id'],
            'consent'         => ['accepted'],
        ]);

        $paymentChannelId = (int) $data['payment_channel'];
        unset($data['payment_channel'], $data['consent']);

        $isAjax = $request->ajax() || $request->wantsJson();

        // Resolve channel to check slug before creating anything
        $channel = \App\Models\PaymentChannel::find($paymentChannelId);
        if (!$channel || !$channel->is_active) {
            $msg = 'Selected payment method is not available.';
            return $isAjax
                ? response()->json(['status' => 'error', 'message' => $msg], 422)
                : back()->withInput()->with('error', $msg);
        }

        // ── Paynow: wrap booking + payment initiation in a transaction.
        //    If Paynow rejects the request, the whole thing rolls back — no orphan bookings.
        if ($channel->slug === 'paynow') {
            $paymentResult = null;
            $booking       = null;

            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($data, $request, $paymentChannelId, &$booking, &$paymentResult) {
                    $booking       = $this->bookings->createFromCart($data, $request);
                    $paymentResult = $this->payments->startPayment($booking, $paymentChannelId);

                    // Roll back if Paynow didn't give us a redirect URL
                    if ($paymentResult['status'] !== 'success' || empty($paymentResult['redirecturl'])) {
                        throw new \RuntimeException($paymentResult['message'] ?? 'Paynow payment initiation failed.');
                    }
                });
            } catch (\RuntimeException $e) {
                // Booking was rolled back — show error, keep cart intact
                $msg = $e->getMessage();
                return $isAjax
                    ? response()->json(['status' => 'error', 'message' => $msg], 422)
                    : back()->withInput()->with('error', $msg);
            }

            // Success — cart is still intact at this point, clear it now
            $this->bookings->clearCart();

            // Success — redirect to Paynow
            if ($isAjax) {
                return response()->json([
                    'status'      => 'success',
                    'redirecturl' => $paymentResult['redirecturl'],
                    'message'     => 'Redirecting to Paynow…',
                ]);
            }
            return redirect($paymentResult['redirecturl']);
        }

        // ── Manual / other channels: create booking normally, no payment gate ──
        $booking       = $this->bookings->createFromCart($data, $request);
        $paymentResult = $this->payments->startPayment($booking, $paymentChannelId);
        $this->bookings->clearCart();
        $confirmUrl    = route('public.book.confirmation', ['code' => $booking->booking_code]);

        if ($isAjax) {
            return response()->json([
                'status'   => 'success',
                'redirect' => $confirmUrl,
                'message'  => $paymentResult['message'] ?? 'Booking received.',
            ]);
        }

        return redirect($confirmUrl)
            ->with('status', $paymentResult['message'] ?? 'Booking received. Payment is pending admin verification.');
    }

    public function confirmation(string $code): View|RedirectResponse
    {
        $booking = $this->bookings->findByCode($code);
        if (!$booking) {
            return redirect()->route('home')->with('error', 'Booking not found.');
        }
        return view('public.book-confirmation', ['booking' => $booking]);
    }

    public function paymentCallback(string $uuid): RedirectResponse
    {
        $result = $this->payments->checkPaymentByUuid($uuid);
        $transaction = PaynowTransaction::where('uuid', $uuid)
            ->with('booking')
            ->first();

        if ($transaction && $result['status'] === 'success') {
            return redirect()
                ->route('public.book.confirmation', ['code' => $transaction->booking->booking_code])
                ->with('status', 'Payment successful!');
        }

        if ($transaction) {
            return redirect()
                ->route('public.book.confirmation', ['code' => $transaction->booking->booking_code])
                ->with('error', $result['message'] ?? 'Payment verification failed.');
        }

        return redirect()->route('home')->with('error', 'Payment verification failed');
    }
}
