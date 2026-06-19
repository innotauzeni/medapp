@extends('layouts.public')
@section('title', 'Complete your booking')

@section('content')
<div class="container py-5">
    <h1 class="h3 mb-1">Complete your booking</h1>
    <p class="text-muted">Please confirm your details. Our team will contact you within 1 business day.</p>

    {{-- Paynow redirect overlay (hidden by default) --}}
    <div id="paynow-overlay" style="display:none; position:fixed; inset:0; z-index:9999;
        background:rgba(0,0,0,.65); backdrop-filter:blur(3px);
        align-items:center; justify-content:center; flex-direction:column; gap:16px;">
        <div style="background:#fff; border-radius:16px; padding:40px 48px; text-align:center; max-width:360px; width:90%;">
            <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
                <span class="visually-hidden">Loading…</span>
            </div>
            <h5 class="mb-1 fw-bold">Redirecting to Paynow</h5>
            <p class="text-muted small mb-0">Please wait — you are being redirected to Paynow to complete your payment securely.</p>
        </div>
    </div>

    <form id="booking-form" method="POST" action="{{ route('public.book.store') }}" class="row g-4 mt-1">
        @csrf

        {{-- Paynow channel IDs for JS detection --}}
        <script id="paynow-channel-ids" type="application/json">
            [{{ $paymentChannels->where('slug','paynow')->pluck('id')->implode(',') }}]
        </script>

        <div class="col-lg-8">
            <div class="card-er card-er-pad">
                <h2 class="h6 mb-3">Your details</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First name *</label>
                        <input name="first_name" value="{{ old('first_name') }}"
                               class="form-control @error('first_name') is-invalid @enderror" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last name *</label>
                        <input name="last_name" value="{{ old('last_name') }}"
                               class="form-control @error('last_name') is-invalid @enderror" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input name="phone" value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror" required>
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
                        <label class="form-label">Payment method *</label>
                        <select id="payment-channel-select" name="payment_channel"
                                class="form-select @error('payment_channel') is-invalid @enderror" required>
                            <option value="">Select payment method</option>
                            @foreach($paymentChannels as $channel)
                                <option value="{{ $channel->id }}"
                                        data-slug="{{ $channel->slug }}"
                                        @selected(old('payment_channel') == $channel->id)>
                                    {{ $channel->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_channel')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        {{-- Paynow info hint --}}
                        <div id="paynow-hint" class="mt-2 p-2 rounded small"
                             style="display:none; background:#f0f7ff; border:1px solid #b8d4f5; color:#1a4a8a;">
                            <i class="bi bi-shield-check me-1"></i>
                            You will be redirected to <strong>Paynow</strong> to complete payment securely.
                            Your booking is saved first — you can track it any time with your booking code.
                        </div>
                        {{-- Manual payment hint --}}
                        <div id="manual-hint" class="mt-2 p-2 rounded small"
                             style="display:none; background:#fff8e6; border:1px solid #f5d87e; color:#7a5500;">
                            <i class="bi bi-bank me-1"></i>
                            Our team will send you bank transfer details after confirming your booking.
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Additional notes</label>
                        <textarea name="notes" rows="3" class="form-control"
                                  placeholder="Any special requirements, group size, scheduling preferences...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="consent" value="1" id="consent"
                                   class="form-check-input @error('consent') is-invalid @enderror" required>
                            <label for="consent" class="form-check-label">
                                I consent to ER Medics contacting me about this booking.
                            </label>
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
                            <div class="text-muted">{{ $row['course']->code }} &middot; {{ $row['quantity'] }}</div>
                            @if ($row['schedule'])
                                <div class="text-muted">{{ $row['schedule']->start_date->format('d M Y') }}</div>
                            @endif
                        </div>
                        <div class="fw-semibold">{{ format_money($row['subtotal']) }}</div>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span><span>{{ format_money($total) }}</span>
                </div>
                <button id="submit-btn" class="btn btn-primary w-100 mt-3">
                    <i class="bi bi-bag-check me-1"></i>
                    <span id="submit-label">Submit booking</span>
                </button>
                <a href="{{ route('public.cart') }}" class="btn btn-soft w-100 mt-2">
                    <i class="bi bi-arrow-left me-1"></i> Back to cart
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    var form      = document.getElementById('booking-form');
    var overlay   = document.getElementById('paynow-overlay');
    var sel       = document.getElementById('payment-channel-select');
    var submitBtn = document.getElementById('submit-btn');
    var submitLbl = document.getElementById('submit-label');
    var paynowHint = document.getElementById('paynow-hint');
    var manualHint = document.getElementById('manual-hint');

    function selectedSlug() {
        var opt = sel.options[sel.selectedIndex];
        return opt ? (opt.dataset.slug || '') : '';
    }

    function updateHints() {
        var slug = selectedSlug();
        paynowHint.style.display = slug === 'paynow' ? 'block' : 'none';
        manualHint.style.display = slug === 'manual' ? 'block' : 'none';
        submitLbl.textContent    = slug === 'paynow' ? 'Submit & Pay with Paynow' : 'Submit booking';
    }

    sel.addEventListener('change', updateHints);
    updateHints(); // handle old() repopulation on validation fail

    form.addEventListener('submit', function (e) {
        if (selectedSlug() !== 'paynow') return; // normal POST for manual/other

        e.preventDefault();

        // Show spinner
        overlay.style.display = 'flex';
        submitBtn.disabled = true;

        var formData = new FormData(form);

        fetch(form.action, {
            method : 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept'           : 'application/json',
            },
            body   : formData,
        })
        .then(function (res) {
            if (res.status === 422) {
                return res.json().then(function (data) {
                    overlay.style.display = 'none';
                    submitBtn.disabled    = false;
                    displayErrors(data.errors || {});
                });
            }
            return res.json().then(function (data) {
                if (data.redirecturl) {
                    window.location.href = data.redirecturl;
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    overlay.style.display = 'none';
                    submitBtn.disabled    = false;
                    alert(data.message || 'Payment could not be started. Please try again.');
                }
            });
        })
        .catch(function () {
            overlay.style.display = 'none';
            submitBtn.disabled    = false;
            alert('Network error. Please check your connection and try again.');
        });
    });

    function displayErrors(errors) {
        // Clear old errors
        form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        form.querySelectorAll('.js-invalid-feedback').forEach(function (el) { el.remove(); });

        Object.keys(errors).forEach(function (field) {
            var input = form.querySelector('[name="' + field + '"]');
            if (!input) return;
            input.classList.add('is-invalid');
            var fb = document.createElement('div');
            fb.className = 'invalid-feedback js-invalid-feedback';
            fb.textContent = errors[field][0];
            input.parentNode.appendChild(fb);
        });

        var first = form.querySelector('.is-invalid');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
})();
</script>
@endpush

@endsection
