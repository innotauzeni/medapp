@extends('layouts.public')
@section('title', 'Booking status')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if (!$booking)
                {{-- ── NOT FOUND ── --}}
                <div class="card-er card-er-pad text-center">
                    <div class="verify-icon danger" style="margin: 0 auto 12px;">
                        <i class="bi bi-x-octagon-fill"></i>
                    </div>
                    <h1 class="h4">Booking not found</h1>
                    <p class="text-muted small">We couldn't find a booking with code <code>{{ $code }}</code>. Please double-check and try again.</p>
                    <a href="{{ route('track.form') }}" class="btn btn-soft">← Try again</a>
                </div>

            @else
                {{-- ── FLASH MESSAGES ── --}}
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                        <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @php
                    $statusCls = 'status-' . $booking->status;
                    $statusLabel = [
                        'pending'   => 'Pending review',
                        'contacted' => 'Contacted by our team',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'converted' => 'Enrolled',
                    ][$booking->status] ?? $booking->status;
                @endphp

                {{-- ── BOOKING HEADER ── --}}
                <div class="card-er card-er-pad">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <div class="small text-muted">Booking reference</div>
                            <div class="fs-5 fw-bold" style="font-family:monospace; color:var(--er-primary-600);">
                                {{ $booking->booking_code }}
                            </div>
                        </div>
                        <span class="badge {{ $statusCls }}" style="padding:8px 14px;font-size:.85rem;border-radius:999px;">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <h2 class="h6 mb-1">Hi {{ $booking->first_name }},</h2>
                    <p class="text-muted small mb-0">
                        @switch ($booking->status)
                            @case ('pending')   We've received your booking and our team will reach out within 1 business day. @break
                            @case ('contacted') We've contacted you — please check your phone and email. @break
                            @case ('confirmed') Your booking is confirmed. We'll see you on training day! @break
                            @case ('cancelled') This booking has been cancelled. Contact us if this is unexpected. @break
                            @case ('converted') You've been enrolled — your records have been created in our system. @break
                            @default            Status: {{ $booking->status }}.
                        @endswitch
                    </p>
                </div>

                {{-- ── PAYMENT STATUS ── --}}
                @php
                    $bookingPayments = $booking->payments->sortByDesc('id');
                @endphp

                <div class="card-er card-er-pad mt-3">
                    <h2 class="h6 mb-3"><i class="bi bi-credit-card me-2"></i>Payment</h2>

                    @if ($bookingPayments->isEmpty())
                        <p class="text-muted small mb-0">No payment record found.</p>
                    @else
                        @foreach ($bookingPayments as $payment)
                            @php
                                $isPaid    = strtoupper($payment->status) === 'PAID';
                                $isPending = in_array(strtoupper($payment->status), ['PENDING', 'AWAITING DELIVERY']);
                                $isFailed  = in_array(strtoupper($payment->status), ['FAILED', 'CANCELLED', 'DISPUTED']);
                                $isPaynow  = $payment->paymentChannel?->slug === 'paynow';
                                $receipt   = $payment->receipts->first();
                            @endphp

                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap
                                        border rounded p-3 mb-2"
                                 style="background:{{ $isPaid ? '#f0fdf4' : ($isFailed ? '#fef2f2' : '#fafafa') }}; border-color:{{ $isPaid ? '#86efac' : ($isFailed ? '#fca5a5' : '#e5e7eb') }} !important;">

                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-semibold small">
                                            {{ $payment->paymentChannel?->name ?? 'Unknown' }}
                                        </span>
                                        <span class="badge status-{{ strtolower($payment->status) }}">
                                            {{ $payment->status }}
                                        </span>
                                    </div>
                                    <div class="fs-5 fw-bold" style="color:var(--er-primary-600);">
                                        {{ format_money($payment->total_ammount) }}
                                    </div>
                                    <div class="small text-muted mt-1">
                                        {{ $payment->created_at->format('d M Y, H:i') }}
                                    </div>

                                    {{-- Status-specific message --}}
                                    @if ($isPaid)
                                        <div class="small mt-1" style="color:#16a34a;">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Payment received — thank you!
                                        </div>
                                    @elseif ($isPending && $isPaynow)
                                        <div class="small mt-1 text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Waiting for Paynow to confirm. Click <strong>Check status</strong> to refresh.
                                        </div>
                                    @elseif ($isFailed)
                                        <div class="small mt-1" style="color:#dc2626;">
                                            <i class="bi bi-x-circle-fill me-1"></i>
                                            Payment was not completed.
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex flex-column gap-2 align-items-end">
                                    {{-- Check payment button (Paynow pending only) --}}
                                    @if (!$isPaid && $isPaynow && $payment->uuid)
                                        <form method="POST"
                                              action="{{ route('track.check-payment', $booking->booking_code) }}">
                                            @csrf
                                            <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-arrow-clockwise me-1"></i> Check status
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Download receipt --}}
                                    @if ($receipt)
                                        <a href="{{ route('track.receipt.download', [$booking->booking_code, $receipt->id]) }}"
                                           class="btn btn-soft btn-sm" target="_blank">
                                            <i class="bi bi-file-earmark-pdf me-1"></i>
                                            Download receipt
                                            <span class="small text-muted ms-1">({{ $receipt->receipt_number }})</span>
                                        </a>
                                    @elseif ($isPaid)
                                        <span class="small text-muted">
                                            <i class="bi bi-hourglass-split me-1"></i> Receipt being generated…
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- ── COURSES ── --}}
                <div class="card-er card-er-pad mt-3">
                    <h2 class="h6 mb-3">Courses booked</h2>
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking->items as $i)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $i->course?->title }}</div>
                                        <div class="small text-muted">
                                            {{ $i->course?->code }}
                                            @if ($i->schedule)
                                                · {{ $i->schedule->start_date->format('d M Y') }}
                                                @if ($i->schedule->location)
                                                    · {{ $i->schedule->location->name }}
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">{{ $i->quantity }}</td>
                                    <td class="text-end">{{ format_money($i->unit_price * $i->quantity) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">{{ format_money($booking->total_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ── ACTIVITY TIMELINE ── --}}
                @if ($booking->statusLogs->isNotEmpty())
                    <div class="card-er card-er-pad mt-3">
                        <h2 class="h6 mb-3">Activity</h2>
                        <div class="timeline">
                            @foreach ($booking->statusLogs as $log)
                                <div class="timeline-item">
                                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                        <div>
                                            <div class="fw-semibold small">
                                                @if ($log->from_status && $log->from_status !== $log->to_status)
                                                    {{ ucfirst($log->from_status) }} →
                                                    <span style="color:var(--er-primary-600);">{{ ucfirst($log->to_status) }}</span>
                                                @else
                                                    Note <span class="text-muted">({{ ucfirst($log->to_status) }})</span>
                                                @endif
                                            </div>
                                            @if ($log->comment)
                                                <div class="small text-muted">{{ $log->comment }}</div>
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="text-center mt-3">
                    <a href="{{ route('track.form') }}" class="btn btn-soft btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Track another booking
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
