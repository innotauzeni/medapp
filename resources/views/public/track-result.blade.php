@extends('layouts.public')
@section('title', 'Booking status')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if (!$booking)
                <div class="card-er card-er-pad text-center">
                    <div class="verify-icon danger" style="margin: 0 auto 12px;"><i class="bi bi-x-octagon-fill"></i></div>
                    <h1 class="h4">Booking not found</h1>
                    <p class="text-muted small">We couldn't find a booking with code <code>{{ $code }}</code>. Please double-check and try again.</p>
                    <a href="{{ route('track.form') }}" class="btn btn-soft">← Try again</a>
                </div>
            @else
                @php
                    $statusCls = 'status-' . $booking->status;
                    $statusLabel = ['pending' => 'Pending review', 'contacted' => 'Contacted by our team', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled', 'converted' => 'Enrolled — see your dashboard'][$booking->status] ?? $booking->status;
                @endphp
                <div class="card-er card-er-pad">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <div class="small text-muted">Booking reference</div>
                            <div class="fs-5 fw-bold"><code>{{ $booking->booking_code }}</code></div>
                        </div>
                        <span class="badge {{ $statusCls }}" style="padding:8px 14px;font-size:.85rem;border-radius:999px;">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <h2 class="h6 mb-2">Hi {{ $booking->first_name }},</h2>
                    <p class="text-muted small">
                        @switch ($booking->status)
                            @case ('pending')        We've received your booking and our team will reach out within 1 business day. @break
                            @case ('contacted')      We've contacted you — please check your phone and email. @break
                            @case ('confirmed')      Your booking is confirmed. We'll see you on training day! @break
                            @case ('cancelled')      This booking has been cancelled. Contact us if this is unexpected. @break
                            @case ('converted')      You've been enrolled — your records have been created in our system. @break
                            @default                 Status: {{ $booking->status }}.
                        @endswitch
                    </p>
                </div>

                <div class="card-er card-er-pad mt-3">
                    <h2 class="h6 mb-3">Courses booked</h2>
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Course</th><th class="text-end">Qty</th><th class="text-end">Subtotal</th></tr></thead>
                        <tbody>
                            @foreach ($booking->items as $i)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $i->course?->title }}</div>
                                        <div class="small text-muted">{{ $i->course?->code }}@if ($i->schedule) · {{ $i->schedule->start_date->format('d M Y') }}@endif</div>
                                    </td>
                                    <td class="text-end">{{ $i->quantity }}</td>
                                    <td class="text-end">{{ format_money($i->unit_price * $i->quantity) }}</td>
                                </tr>
                            @endforeach
                            <tr><td colspan="2" class="text-end fw-bold">Total</td><td class="text-end fw-bold">{{ format_money($booking->total_amount) }}</td></tr>
                        </tbody>
                    </table>
                </div>

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
                                                {{ ucfirst($log->from_status) }} → <span class="text-er-primary">{{ ucfirst($log->to_status) }}</span>
                                            @else
                                                Note ({{ ucfirst($log->to_status) }})
                                            @endif
                                        </div>
                                        @if ($log->comment)<div class="small text-muted">{{ $log->comment }}</div>@endif
                                    </div>
                                    <div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="text-center mt-3">
                    <a href="{{ route('track.form') }}" class="btn btn-soft btn-sm">← Track another</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
