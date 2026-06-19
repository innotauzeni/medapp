@extends('layouts.public')
@section('title', 'Booking confirmed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card-er card-er-pad text-center">
                <div class="verify-icon ok" style="margin: 0 auto 12px;"><i class="bi bi-bag-check-fill"></i></div>
                <h1 class="h3 mb-1">Booking received!</h1>
                <p class="text-muted">Thank you {{ $booking->first_name }} — we'll be in touch within 1 business day.</p>

                <div class="my-4">
                    <div class="small text-muted text-uppercase" style="letter-spacing: .08em;">Your booking reference</div>
                    <div class="fs-3 fw-bold mt-1" style="letter-spacing: 1px; color: var(--er-primary-600);">
                        {{ $booking->booking_code }}
                    </div>
                    <button class="btn btn-soft btn-sm mt-2" type="button" onclick="navigator.clipboard.writeText('{{ $booking->booking_code }}'); this.innerHTML='<i class=\'bi bi-check2 me-1\'></i>Copied'">
                        <i class="bi bi-clipboard me-1"></i> Copy code
                    </button>
                </div>

                <p class="small text-muted">A confirmation email has been sent to <strong>{{ $booking->email }}</strong>.<br>
                Track your booking status at any time using the code above.</p>

                <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
                    <a href="{{ route('track.show', ['code' => $booking->booking_code]) }}" class="btn btn-primary"><i class="bi bi-search me-1"></i> Track this booking</a>
                    <a href="{{ route('public.courses') }}" class="btn btn-soft">Browse more courses</a>
                </div>
            </div>

            <div class="card-er card-er-pad mt-3">
                <h2 class="h6 mb-3">What you booked</h2>
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
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">{{ format_money($booking->total_amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

                <div class="card-er card-er-pad mt-3 text-start">
                    <h2 class="h6 mb-3">Payment status</h2>
                    @php $bookingPayments = $booking->payments()->with('paymentChannel')->get(); @endphp
                    @if ($bookingPayments->isEmpty())
                        <p class="text-muted mb-0">No payment record found yet.</p>
                    @else
                        @foreach ($bookingPayments as $payment)
                            <div class="d-flex justify-content-between gap-3 align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <div class="fw-semibold">{{ $payment->paymentChannel?->name ?? 'Unknown' }}</div>
                                    <div class="small text-muted">{{ format_money($payment->total_ammount) }} · {{ $payment->currency }}</div>
                                </div>
                                <span class="badge status-{{ strtolower($payment->status) }}">{{ $payment->status }}</span>
                            </div>
                        @endforeach

                        @php
                            $pendingPaynow = $bookingPayments->first(fn($p) =>
                                strtoupper($p->status) !== 'PAID'
                                && $p->paymentChannel?->slug === 'paynow'
                                && $p->uuid
                            );
                        @endphp
                        @if ($pendingPaynow)
                            <a href="{{ route('public.paynow.check', ['uuid' => $pendingPaynow->uuid]) }}"
                               class="btn btn-soft btn-sm w-100 mt-2">
                                <i class="bi bi-arrow-clockwise me-1"></i> Check payment status
                            </a>
                        @endif
                    @endif
                </div>

            {{-- Clear localStorage cart after success --}}
            @push('scripts')
            <script>
                try { localStorage.removeItem('er-cart'); } catch (e) {}
            </script>
            @endpush
        </div>
    </div>
</div>
@endsection
