<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BookPayment;
use App\Models\BookReceipt;
use App\Services\BookPaymentService;
use App\Services\BookingService;
use App\Services\BookReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function __construct(
        private readonly BookingService    $bookings,
        private readonly BookPaymentService $payments,
        private readonly BookReceiptService $receipts,
    ) {}

    public function form(): View
    {
        return view('public.track-form');
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:64']]);
        return redirect()->route('track.show', ['code' => trim($data['code'])]);
    }

    public function show(string $code): View
    {
        $booking = $this->bookings->findByCode($code);
        return view('public.track-result', ['booking' => $booking, 'code' => $code]);
    }

    /**
     * Public: check a specific payment status (Paynow poll).
     * Requires the booking_code to verify ownership — no auth needed.
     */
    public function checkPayment(Request $request, string $code): RedirectResponse
    {
        $booking = $this->bookings->findByCode($code);

        if (!$booking) {
            return redirect()->route('track.show', ['code' => $code])
                ->with('error', 'Booking not found.');
        }

        $paymentId = $request->integer('payment_id');
        $payment   = $booking->payments()
            ->with('paymentChannel')
            ->where('id', $paymentId)
            ->first();

        if (!$payment) {
            return redirect()->route('track.show', ['code' => $code])
                ->with('error', 'Payment record not found.');
        }

        $result = $this->payments->checkPayment($payment);

        $flashKey = $result['status'] === 'success' ? 'status' : (
            $result['status'] === 'pending' ? 'info' : 'error'
        );

        return redirect()->route('track.show', ['code' => $code])
            ->with($flashKey, $result['message']);
    }

    /**
     * Public: download a receipt PDF.
     * Requires the booking_code to verify ownership — no auth needed.
     */
    public function downloadReceipt(string $code, int $receiptId): \Symfony\Component\HttpFoundation\Response
    {
        $booking = $this->bookings->findByCode($code);

        abort_if(!$booking, 404, 'Booking not found.');

        $receipt = BookReceipt::where('id', $receiptId)
            ->where('booking_id', $booking->id)
            ->firstOrFail();

        $pdf      = $this->receipts->renderPdf($receipt);
        $filename = 'receipt-' . $receipt->receipt_number . '.pdf';

        return $pdf->download($filename);
    }
}
