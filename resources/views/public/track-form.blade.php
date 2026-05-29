@extends('layouts.public')
@section('title', 'Track your booking')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card-er card-er-pad">
                <h1 class="h4 text-center mb-1">Track your booking</h1>
                <p class="text-muted text-center small mb-4">Enter the booking reference we emailed you (e.g. <code>ERM-BOOK-26-ABC123</code>).</p>

                <form method="POST" action="{{ route('track.submit') }}">
                    @csrf
                    <input name="code" class="form-control form-control-lg text-center fw-semibold mb-3" style="letter-spacing:.04em" placeholder="ERM-BOOK-26-XXXXXX" required autofocus>
                    <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Track booking</button>
                </form>
                <div class="text-center mt-3 small text-muted">
                    Lost your code? Call us on <strong>0784701050</strong>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
