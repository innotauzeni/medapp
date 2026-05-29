@extends('layouts.app')
@section('title', 'Users')
@section('header', 'Users &amp; access')
@section('subheader', 'Admin, account, and customer accounts')

@section('header-actions')
    @can('users.create')<a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> New user</a>@endcan
@endsection

@section('content')
    <div class="card-er card-er-pad mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Search name or email...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">Any role</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" @selected(($filters['role'] ?? '') === $r->name)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead><tr><th>User</th><th>Email</th><th>Roles</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($paginator as $u)
                        @php
                            $parts = preg_split('/\s+/', trim($u->name));
                            $init = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar-bubble">{{ $init }}</span>
                                    <a href="{{ route('users.show', $u) }}" class="text-decoration-none fw-semibold">{{ $u->name }}</a>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @forelse ($u->roles as $r)
                                    <span class="badge badge-soft badge-soft-primary me-1">{{ $r->name }}</span>
                                @empty
                                    <span class="text-muted small">No roles</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($u->is_active)<span class="badge badge-soft badge-soft-success">Active</span>
                                @else<span class="badge badge-soft badge-soft-muted">Inactive</span>@endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('users.show', $u) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-eye"></i></a>
                                    @can('users.update')<a href="{{ route('users.edit', $u) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-pencil"></i></a>@endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state"><i class="bi bi-person-x"></i> No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
