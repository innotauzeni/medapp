@extends('layouts.app')
@section('title', 'Trainers')
@section('header', 'Trainers')
@section('subheader', 'Manage trainers and course assignments')

@section('header-actions')
    @can('trainers.create')<a href="{{ route('trainers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New trainer</a>@endcan
@endsection

@section('content')
    <div class="card-er card-er-pad mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name, email, specialty...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">Any</option>
                    <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Active</option>
                    <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead><tr><th>Trainer</th><th>Email</th><th>Phone</th><th>Specialty</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($paginator as $t)
                        @php $init = strtoupper(substr($t->first_name, 0, 1) . substr($t->last_name, 0, 1)); @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar-bubble">{{ $init }}</span>
                                    <div>
                                        <a href="{{ route('trainers.show', $t) }}" class="text-decoration-none fw-semibold">{{ $t->full_name }}</a>
                                        @if ($t->qualifications)<div class="small text-muted">{{ $t->qualifications }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $t->email ?? '—' }}</td>
                            <td>{{ $t->phone ?? '—' }}</td>
                            <td>{{ $t->specialty ?? '—' }}</td>
                            <td>
                                @if ($t->is_active)<span class="badge badge-soft badge-soft-success">Active</span>
                                @else<span class="badge badge-soft badge-soft-muted">Inactive</span>@endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('trainers.show', $t) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-eye"></i></a>
                                    @can('trainers.update')<a href="{{ route('trainers.edit', $t) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-pencil"></i></a>@endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state"><i class="bi bi-person-x"></i> No trainers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
