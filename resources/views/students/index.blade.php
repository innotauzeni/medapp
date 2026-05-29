@extends('layouts.app')
@section('title', 'Students')
@section('header', 'Students')
@section('subheader', 'Manage student profiles, contacts and training history')

@section('header-actions')
    @can('students.create')
        <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> New student</a>
    @endcan
@endsection

@section('content')
    <div class="card-er card-er-pad mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name, email, student number, ID...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active"   @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="archived" @selected(($filters['status'] ?? '') === 'archived')>Archived</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('students.index') }}" class="btn btn-soft w-100">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Number</th>
                        <th>Contact</th>
                        <th>ID</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paginator as $s)
                        @php
                            $init = strtoupper(substr($s->first_name, 0, 1) . substr($s->last_name, 0, 1));
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($s->profile_photo_path)
                                        <img src="{{ asset('storage/'.$s->profile_photo_path) }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                    @else
                                        <span class="avatar-bubble">{{ $init }}</span>
                                    @endif
                                    <div>
                                        <a href="{{ route('students.show', $s) }}" class="text-decoration-none fw-semibold">{{ $s->full_name }}</a>
                                        @if ($s->city)<div class="small text-muted">{{ $s->city }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $s->student_number }}</code></td>
                            <td>
                                <div class="small">{{ $s->email ?? '—' }}</div>
                                <div class="small text-muted">{{ $s->phone ?? '' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-soft badge-soft-muted">{{ strtoupper($s->id_type) }}</span>
                                <span class="small">{{ $s->id_number }}</span>
                            </td>
                            <td>
                                @if ($s->status === 'active')
                                    <span class="badge badge-soft badge-soft-success">Active</span>
                                @else
                                    <span class="badge badge-soft badge-soft-muted">Archived</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('students.show', $s) }}" class="btn btn-soft btn-sm btn-icon-only" title="View"><i class="bi bi-eye"></i></a>
                                    @can('students.update')
                                        <a href="{{ route('students.edit', $s) }}" class="btn btn-soft btn-sm btn-icon-only" title="Edit"><i class="bi bi-pencil"></i></a>
                                    @endcan
                                    @can('students.delete')
                                        <form action="{{ route('students.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this student permanently?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-soft btn-sm btn-icon-only" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state"><i class="bi bi-people"></i> No students found. Try clearing filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
