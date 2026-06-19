@extends('layouts.public')
@section('title', 'Your cart')

@section('content')
<div class="container py-5">
    <h1 class="h3 mb-4">Your cart ({{ $count }})</h1>

    @if (empty($items))
        <div class="card-er card-er-pad text-center">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <h2 class="h5 mt-2">Your cart is empty</h2>
            <p class="text-muted small">Browse our course catalogue and add the courses you'd like to enrol in.</p>
            <a href="{{ route('public.courses') }}" class="btn btn-primary"><i class="bi bi-mortarboard me-1"></i> Browse courses</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach ($items as $row)
                    <div class="cart-row">
                        <div class="thumb"><i class="bi bi-journal-text"></i></div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold">{{ $row['course']->title }}</div>
                            <div class="small text-muted">
                                {{ $row['course']->code }}
                                @if ($row['schedule'])
                                    · {{ $row['schedule']->start_date->format('d M Y') }}@if ($row['schedule']->location) · {{ $row['schedule']->location->name }}@endif
                                @endif
                            </div>
                            <div class="small text-muted">{{ format_money($row['course']->fee) }} each</div>
                        </div>
                        <form method="POST" action="{{ route('public.cart.update', ['key' => $row['key']]) }}" class="d-flex align-items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $row['quantity'] }}" min="0" max="50"
                                   class="form-control form-control-sm" style="width: 70px;"
                                   onchange="this.form.submit()">
                        </form>
                        <div class="fw-semibold text-end" style="min-width: 100px;">{{ format_money($row['subtotal']) }}</div>
                        <form method="POST" action="{{ route('public.cart.remove', ['key' => $row['key']]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-soft btn-sm btn-icon-only" title="Remove"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                @endforeach

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('public.courses') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i> Continue shopping</a>
                    <form method="POST" action="{{ route('public.cart.clear') }}" onsubmit="return confirm('Clear all items?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-soft text-danger"><i class="bi bi-trash me-1"></i> Clear cart</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-er card-er-pad" style="position: sticky; top: 80px;">
                    <h2 class="h6 mb-3">Order summary</h2>
                    <div class="d-flex justify-content-between small mb-1"><span>Items</span><span>{{ $count }}</span></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span>{{ format_money($total) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>{{ format_money($total) }}</span></div>
                    <a href="{{ route('public.book.form') }}" class="btn btn-primary w-100 mt-3"><i class="bi bi-bag-check me-1"></i> Proceed to booking</a>
                    {{-- <div class="text-muted small mt-2 text-center">No payment required at booking. We'll contact you to finalise.</div> --}}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
