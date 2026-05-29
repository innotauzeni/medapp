@extends('layouts.guest')
@section('title', 'Reset password')

@section('content')
    <h1 class="h4 mb-3 text-center">Forgot password?</h1>
    <p class="small text-muted text-center mb-4">Enter your email and we will send you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Email password reset link</button>
        <div class="text-center small mt-3"><a href="{{ route('login') }}">Back to sign in</a></div>
    </form>
@endsection
