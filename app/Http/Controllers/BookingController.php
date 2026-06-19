<?php

namespace App\Http\Controllers;

use App\Models\BookReceipt;
use App\Models\User;
use App\Services\BookPaymentService;
use App\Services\BookingService;
use App\Services\BookReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
        private readonly BookPaymentService $payments,
        private readonly BookReceiptService $receipts,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status', 'assigned_to']);
        $paginator = $this->bookings->list($filters, $request->integer('per_page', 15) ?: 15);
        $assignees = User::role(['Admin', 'Account'])->orderBy('name')->get(['id', 'name']);
        return view('bookings.index', compact('paginator', 'filters', 'assignees'));
    }

    public function show(int $booking): View
    {
        $this->authorize('bookings.view');
        $b = $this->bookings->get($booking);
        $assignees = User::role(['Admin', 'Account'])->orderBy('name')->get(['id', 'name']);
        $payments  = $this->payments->getPaymentsForBooking($booking);
        $receipts  = $this->receipts->forBooking($booking);
        return view('bookings.show', compact('b', 'assignees', 'payments', 'receipts')
            + ['booking' => $b]);
    }

    public function updateStatus(Request $request, int $booking): RedirectResponse
    {
        $this->authorize('bookings.update');
        $data = $request->validate([
            'status'  => ['required', 'in:pending,contacted,confirmed,cancelled,converted'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ]);
        $this->bookings->updateStatus($booking, $data['status'], $data['comment'] ?? null);
        return back()->with('status', 'Status updated.');
    }

    public function addNote(Request $request, int $booking): RedirectResponse
    {
        $this->authorize('bookings.update');
        $data = $request->validate(['comment' => ['required', 'string', 'max:1500']]);
        $this->bookings->addNote($booking, $data['comment']);
        return back()->with('status', 'Note added.');
    }

    public function assign(Request $request, int $booking): RedirectResponse
    {
        $this->authorize('bookings.update');
        $data = $request->validate(['assigned_to' => ['nullable', 'exists:users,id']]);
        $this->bookings->assign($booking, $data['assigned_to'] ?? null);
        return back()->with('status', 'Booking assigned.');
    }

    public function showPayment(int $booking): View
    {
        $this->authorize('bookings.view');
        $b        = $this->bookings->get($booking);
        $payments = $this->payments->getPaymentsForBooking($booking);
        $receipts = $this->receipts->forBooking($booking);
        return view('bookings.payment', compact('b', 'payments', 'receipts')
            + ['booking' => $b]);
    }

    public function downloadReceipt(int $booking, int $receipt): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('bookings.view');
        $r = BookReceipt::where('id', $receipt)
            ->where('booking_id', $booking)
            ->firstOrFail();

        $pdf = $this->receipts->renderPdf($r);
        $filename = 'receipt-' . $r->receipt_number . '.pdf';
        return $pdf->download($filename);
    }

    public function generateReceipt(int $booking, Request $request): RedirectResponse
    {
        $this->authorize('bookings.update');
        $paymentId = $request->integer('payment_id');
        $b = $this->bookings->get($booking);

        $payment = $b->payments()
            ->with(['paymentChannel'])
            ->where('id', $paymentId)
            ->first();

        if (!$payment) {
            return back()->with('error', 'Payment record not found.');
        }

        if (strtoupper($payment->status) !== 'PAID') {
            return back()->with('error', 'Cannot generate a receipt — payment is not marked PAID.');
        }

        $receipt = $this->receipts->generateForPayment($payment);
        return back()->with('status', "Receipt {$receipt->receipt_number} generated successfully.");
    }

    public function checkPayment(Request $request, int $booking): RedirectResponse
    {
        $this->authorize('bookings.update');
        $b = $this->bookings->get($booking);

        if ($request->has('payment_id')) {
            $payment = $b->payments()
                ->with(['paymentChannel'])
                ->where('id', $request->integer('payment_id'))
                ->first();

            if (!$payment) {
                return back()->with('error', 'Payment not found.');
            }

            $result = $this->payments->checkPayment($payment);
            return back()->with($result['status'] === 'success' ? 'status' : 'error', $result['message']);
        }

        $messages = [];
        $hasError = false;
        foreach ($b->payments()->whereRelation('paymentChannel', 'slug', 'paynow')->get() as $payment) {
            $result = $this->payments->checkPayment($payment);
            $messages[] = $result['message'];
            $hasError = $hasError || $result['status'] !== 'success';
        }

        if (empty($messages)) {
            return back()->with('status', 'No Paynow payments found for this booking.');
        }

        return back()->with($hasError ? 'error' : 'status', implode(' ', array_filter($messages)));
    }
}
