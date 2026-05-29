@extends('layouts.public')
@section('title', 'Complete your booking')

@section('content')
<div class="container py-5">
    <h1 class="h3 mb-1">Complete your booking</h1>
    <p class="text-muted">Please confirm your details. Our team will contact you within 1 business day.</p>

    <form method="POST" action="{{ route('public.book.store') }}" class="row g-4 mt-1">
        @csrf
        <div class="col-lg-8">
            <div class="card-er card-er-pad">
                <h2 class="h6 mb-3">Your details</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First name *</label>
                        <input name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last name *</label>
                        <input name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ID type *</label>
                        <select name="id_type" class="form-select" required>
                            <option value="id"       @selected(old('id_type', 'id') === 'id')>ID</option>
                            <option value="passport" @selected(old('id_type') === 'passport')>Passport</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">ID / passport number</label>
                        <input name="id_number" value="{{ old('id_number') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Organization</label>
                        <input name="organization" value="{{ old('organization') }}" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input name="city" value="{{ old('city') }}" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Additional notes</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Any special requirements, group size, scheduling preferences...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="consent" value="1" id="consent" class="form-check-input @error('consent') is-invalid @enderror" required>
                            <label for="consent" class="form-check-label">I consent to ER Medics contacting me about this booking.</label>
                            @error('consent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-er card-er-pad" style="position: sticky; top: 80px;">
                <h2 class="h6 mb-3">Booking summary</h2>
                @foreach ($items as $row)
                    <div class="d-flex justify-content-between mb-2 small">
                        <div>
                            <div class="fw-semibold">{{ $row['course']->title }}</div>
                            <div class="text-muted">{{ $row['course']->code }} × {{ $row['quantity'] }}</div>
                            @if ($row['schedule'])
                                <div class="text-muted">{{ $row['schedule']->start_date->format('d M Y') }}</div>
                            @endif
                        </div>
                        <div class="fw-semibold">{{ format_money($row['subtotal']) }}</div>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>{{ format_money($total) }}</span></div>
                <button class="btn btn-primary w-100 mt-3"><i class="bi bi-bag-check me-1"></i> Submit booking</button>
                <a href="{{ route('public.cart') }}" class="btn btn-soft w-100 mt-2"><i class="bi bi-arrow-left me-1"></i> Back to cart</a>
            </div>
        </div>
    </form>
</div>
@endsection
