@extends('layouts.guest')
@section('title', 'Confirm password')

@section('content')
    <h1 class="h4 mb-3 text-center">Confirm your password</h1>
    <p class="small text-muted text-center mb-4">This is a secure area. Please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Confirm</button>
    </form>
@endsection
