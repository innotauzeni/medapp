@extends('layouts.guest')
@section('title', 'Verify email')

@section('content')
    <h1 class="h4 mb-3 text-center">Verify your email</h1>
    <p class="small text-muted mb-3">
        Thanks for signing up! Please verify your email by clicking the link we just sent you.
        If you didn't receive it, we can send another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small">A new verification link has been sent to your email.</div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Resend verification email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted">Log out</button>
        </form>
    </div>
@endsection
