@extends('layouts.app')
@section('title', 'Profile')
@section('header', 'My Profile')

@section('content')
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="content-card">
                <h2 class="h5 mb-1">Profile information</h2>
                <p class="text-muted small mb-3">Update your account's profile information and email address.</p>

                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success small">Profile updated.</div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="form-control @error('name','updateProfileInformation') is-invalid @enderror" required>
                        @error('name','updateProfileInformation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="form-control @error('email','updateProfileInformation') is-invalid @enderror" required>
                        @error('email','updateProfileInformation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-warning small">
                            Your email address is unverified.
                            <button form="send-verification" class="btn btn-link p-0 align-baseline small">Resend verification email</button>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>

                <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="d-none">@csrf</form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="content-card">
                <h2 class="h5 mb-1">Update password</h2>
                <p class="text-muted small mb-3">Use a long, random password to keep your account secure.</p>

                @if (session('status') === 'password-updated')
                    <div class="alert alert-success small">Password updated.</div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current password</label>
                        <input id="current_password" type="password" name="current_password"
                               class="form-control @error('current_password','updatePassword') is-invalid @enderror" autocomplete="current-password">
                        @error('current_password','updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New password</label>
                        <input id="password" type="password" name="password"
                               class="form-control @error('password','updatePassword') is-invalid @enderror" autocomplete="new-password">
                        @error('password','updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control" autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update password</button>
                </form>
            </div>
        </div>

        <div class="col-12">
            <div class="content-card border-danger-subtle">
                <h2 class="h5 mb-1 text-danger">Delete account</h2>
                <p class="text-muted small mb-3">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteAccount">Delete account</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteAccount" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Confirm account deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Enter your password to permanently delete your account. This action cannot be undone.</p>
                    <input type="password" name="password" class="form-control @error('password','userDeletion') is-invalid @enderror" placeholder="Password" required>
                    @error('password','userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete account</button>
                </div>
            </form>
        </div>
    </div>
@endsection
