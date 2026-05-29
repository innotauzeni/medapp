@extends('layouts.app')
@section('title', $user->name)
@section('header', $user->name)
@section('subheader', $user->email)

@section('header-actions')
    @can('users.update')<a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Edit</a>@endcan
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="content-card">
                <h2 class="h6 mb-3">Account</h2>
                <dl class="row mb-0 small">
                    <dt class="col-4 text-muted">Email</dt><dd class="col-8">{{ $user->email }}</dd>
                    <dt class="col-4 text-muted">Phone</dt><dd class="col-8">{{ $user->phone ?? '—' }}</dd>
                    <dt class="col-4 text-muted">Active</dt>
                    <dd class="col-8">@if ($user->is_active)<span class="badge text-bg-success">Yes</span>@else<span class="badge text-bg-secondary">No</span>@endif</dd>
                    <dt class="col-4 text-muted">Created</dt><dd class="col-8">{{ $user->created_at->format('Y-m-d') }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-md-6">
            <div class="content-card">
                <h2 class="h6 mb-3">Roles &amp; permissions</h2>
                <div class="mb-3">
                    @forelse ($user->roles as $r)
                        <span class="badge text-bg-info me-1">{{ $r->name }}</span>
                    @empty
                        <span class="text-muted small">No roles assigned.</span>
                    @endforelse
                </div>
                <details>
                    <summary class="small text-muted">Effective permissions ({{ $user->getAllPermissions()->count() }})</summary>
                    <div class="mt-2">
                        @foreach ($user->getAllPermissions() as $p)
                            <span class="badge text-bg-light text-dark me-1 mb-1">{{ $p->name }}</span>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
    </div>
@endsection
