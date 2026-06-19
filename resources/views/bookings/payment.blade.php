@extends('layouts.app')
@section('title', 'Payment · ' . $booking->booking_code)
@section('header', 'Payment details')
@section('subheader', $booking->booking_code . ' · ' . $booking->full_name)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">

        {{-- Flash messages --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ── PAYMENTS ── --}}
        <div class="card-er card-er-pad">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h2 class="h6 m-0">Payment records</h2>
                @can('bookings.update')
                    @php
                        $hasUnpaidPaynow = collect($payments)->contains(
                            fn($p) => strtoupper($p['status']) !== 'PAID'
                                   && ($p['payment_channel']['slug'] ?? '') === 'paynow'
                        );
                    @endphp
                    @if ($hasUnpaidPaynow)
                        <form method="POST" action="{{ route('bookings.payment.check', $booking) }}">
                            @csrf
                            <button type="submit" class="btn btn-soft btn-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Check all Paynow
                            </button>
                        </form>
                    @endif
                @endcan
            </div>

            @if (empty($payments))
                <div class="text-center py-4">
                    <i class="bi bi-credit-card text-muted" style="font-size:2rem;"></i>
                    <p class="text-muted mt-2 mb-0">No payments found for this booking.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table-er">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Channel</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                @php
                                    $isPaid   = strtoupper($payment['status']) === 'PAID';
                                    $isPaynow = ($payment['payment_channel']['slug'] ?? '') === 'paynow';
                                    $isManual = ($payment['payment_channel']['slug'] ?? '') === 'manual';

                                    // Find receipt for this payment (if any)
                                    $receipt = $receipts->firstWhere('book_payment_id', $payment['id']);
                                @endphp
                                <tr>
                                    <td class="small text-muted">
                                        {{ \Carbon\Carbon::parse($payment['created_at'])->format('d M Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">{{ $payment['payment_channel']['name'] ?? 'Unknown' }}</div>
                                        @if (!empty($payment['uuid']))
                                            <div class="text-muted" style="font-size:.7rem; font-family:monospace;">
                                                {{ substr($payment['uuid'], 0, 16) }}…
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ format_money($payment['total_ammount']) }}</td>
                                    <td>
                                        <span class="badge status-{{ strtolower($payment['status']) }}">
                                            {{ $payment['status'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1 flex-wrap">
                                            {{-- Check payment status --}}
                                            @if (!$isPaid)
                                                @can('bookings.update')
                                                    <form method="POST"
                                                          action="{{ route('bookings.payment.check', $booking) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="payment_id" value="{{ $payment['id'] }}">
                                                        <button type="submit" class="btn btn-soft btn-sm"
                                                                title="{{ $isPaynow ? 'Poll Paynow for status' : 'Mark as paid' }}">
                                                            <i class="bi bi-arrow-clockwise me-1"></i>
                                                            {{ $isPaynow ? 'Check' : 'Mark paid' }}
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif

                                            {{-- Generate receipt (PAID but no receipt yet) --}}
                                            @if ($isPaid && !$receipt)
                                                @can('bookings.update')
                                                    <form method="POST"
                                                          action="{{ route('bookings.payment.generate-receipt', $booking) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="payment_id" value="{{ $payment['id'] }}">
                                                        <button type="submit" class="btn btn-soft btn-sm">
                                                            <i class="bi bi-receipt me-1"></i> Generate receipt
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif

                                            {{-- Download receipt --}}
                                            @if ($receipt)
                                                <a href="{{ route('bookings.receipt.download', [$booking, $receipt->id]) }}"
                                                   class="btn btn-primary btn-sm" target="_blank">
                                                    <i class="bi bi-download me-1"></i> Receipt
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ── RECEIPTS SUMMARY ── --}}
        @if ($receipts->isNotEmpty())
            <div class="card-er mt-3">
                <div class="card-er-head">
                    <h2 class="h6 m-0"><i class="bi bi-receipt me-2"></i>Issued receipts</h2>
                </div>
                <div class="table-responsive">
                    <table class="table-er">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Channel</th>
                                <th class="text-end">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($receipts as $r)
                                <tr>
                                    <td class="fw-semibold" style="font-family:monospace; color:var(--er-primary-600);">
                                        {{ $r->receipt_number }}
                                    </td>
                                    <td class="small text-muted">{{ $r->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ format_money($r->amount) }}</td>
                                    <td>{{ $r->payment->paymentChannel?->name ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('bookings.receipt.download', [$booking, $r->id]) }}"
                                           class="btn btn-soft btn-sm" target="_blank">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ── BOOKING ITEMS ── --}}
        <div class="card-er mt-3">
            <div class="card-er-head"><h2 class="h6 m-0">Booking items</h2></div>
            <div class="table-responsive">
                <table class="table-er">
                    <thead><tr><th>Course</th><th>Schedule</th><th>Qty</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                        @foreach ($booking->items as $i)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $i->course?->title }}</div>
                                    <div class="small text-muted">{{ $i->course?->code }}</div>
                                </td>
                                <td>
                                    @if ($i->schedule)
                                        {{ $i->schedule->start_date->format('d M Y') }}
                                        @if ($i->schedule->location)
                                            <div class="small text-muted">{{ $i->schedule->location->name }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">Open / TBD</span>
                                    @endif
                                </td>
                                <td>{{ $i->quantity }}</td>
                                <td class="text-end">{{ format_money($i->unit_price * $i->quantity) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">{{ format_money($booking->total_amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── SIDEBAR ── --}}
    <div class="col-lg-4">
        <div class="card-er card-er-pad">
            <h2 class="h6 mb-3">Customer</h2>
            <dl class="row mb-0 small">
                <dt class="col-5 text-muted">Name</dt>
                <dd class="col-7">{{ $booking->full_name }}</dd>
                <dt class="col-5 text-muted">Email</dt>
                <dd class="col-7"><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a></dd>
                <dt class="col-5 text-muted">Phone</dt>
                <dd class="col-7"><a href="tel:{{ $booking->phone }}">{{ $booking->phone }}</a></dd>
                <dt class="col-5 text-muted">Booking</dt>
                <dd class="col-7 fw-semibold" style="color:var(--er-primary-600);">{{ $booking->booking_code }}</dd>
                <dt class="col-5 text-muted">Status</dt>
                <dd class="col-7">
                    <span class="badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                </dd>
                <dt class="col-5 text-muted">Receipts</dt>
                <dd class="col-7 fw-semibold">{{ $receipts->count() }}</dd>
            </dl>
        </div>

        <div class="card-er card-er-pad mt-3">
            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-soft w-100">
                <i class="bi bi-arrow-left me-1"></i> Back to booking
            </a>
        </div>
    </div>
</div>
@endsection
