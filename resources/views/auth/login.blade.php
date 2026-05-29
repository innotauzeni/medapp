@extends('layouts.guest')
@section('title', 'Sign in')

@section('content')
    <h1 class="h4 mb-2 text-center">Welcome back</h1>
    <p class="text-muted text-center mb-4 small">Sign in to your ER Medics dashboard</p>

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-floating mb-3">
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="Email" required autofocus autocomplete="username">
            <label for="email">Email address</label>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-floating mb-3">
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Password" required autocomplete="current-password">
            <label for="password">Password</label>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label small" for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small text-muted" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-box-arrow-in-right me-1"></i> Sign in</button>
    </form>
@endsection
