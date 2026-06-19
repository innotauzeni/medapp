<?php

namespace App\Services;

use App\Models\BookPayment;
use App\Models\BookReceipt;
use App\Repositories\BookReceiptRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class BookReceiptService
{
    public function __construct(
        private readonly BookReceiptRepository $receipts,
    ) {}

    /**
     * Generate a receipt for a PAID BookPayment.
     * Idempotent — returns the existing receipt if one already exists.
     */
    public function generateForPayment(BookPayment $payment): BookReceipt
    {
        // Return existing receipt for this payment if already generated
        $existing = BookReceipt::where('book_payment_id', $payment->id)->first();
        if ($existing) {
            return $existing;
        }

        $payment->loadMissing(['booking.items.course', 'paymentChannel']);

        return DB::transaction(function () use ($payment) {
            $receiptNumber = $this->receipts->generateReceiptNumber();

            return BookReceipt::create([
                'booking_id'     => $payment->booking_id,
                'book_payment_id'=> $payment->id,
                'receipt_number' => $receiptNumber,
                'currency'       => $payment->currency ?? currency_code(),
                'amount'         => $payment->total_ammount,
                'description'    => 'Payment received for booking ' . $payment->booking->booking_code,
            ]);
        });
    }

    /**
     * Render the receipt as a DomPDF PDF and return the PDF instance.
     */
    public function renderPdf(BookReceipt $receipt): \Barryvdh\DomPDF\PDF
    {
        $receipt->loadMissing([
            'booking.items.course',
            'booking.items.schedule.location',
            'payment.paymentChannel',
        ]);

        return Pdf::loadView('receipts.pdf', ['receipt' => $receipt])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('margin_top',    0)
            ->setOption('margin_bottom', 0)
            ->setOption('margin_left',   0)
            ->setOption('margin_right',  0)
            ->setOption('dpi', 96);
    }

    /** Return all receipts for a booking (newest first). */
    public function forBooking(int $bookingId): \Illuminate\Database\Eloquent\Collection
    {
        return BookReceipt::where('booking_id', $bookingId)
            ->with(['payment.paymentChannel'])
            ->latest('id')
            ->get();
    }
}
