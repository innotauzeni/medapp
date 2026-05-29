@extends('layouts.app')
@section('title', 'Course schedules')
@section('header', 'Course schedules')
@section('subheader', 'Cohorts and dates for each course')

@section('header-actions')
    @can('schedules.create')<a href="{{ route('schedules.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New schedule</a>@endcan
@endsection

@section('content')
<div class="card-er card-er-pad mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Course</label>
            <select name="course_id" class="form-select">
                <option value="">All courses</option>
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}" @selected((int)($filters['course_id'] ?? 0) === $c->id)>[{{ $c->code }}] {{ $c->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach (['scheduled','in_progress','completed','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ str_replace('_',' ',$s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
    </form>
</div>

<div class="table-wrap">
    <div class="table-responsive">
        <table class="table-er">
            <thead><tr><th>Course</th><th>Start</th><th>End</th><th>Location</th><th>Lead trainer</th><th>Capacity</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($paginator as $s)
                    @php
                        $cls = ['scheduled' => 'badge-soft-info', 'in_progress' => 'badge-soft-warn', 'completed' => 'badge-soft-success', 'cancelled' => 'badge-soft-danger'][$s->status] ?? 'badge-soft-muted';
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $s->course?->title }}</div>
                            <div class="small text-muted">{{ $s->course?->code }}</div>
                        </td>
                        <td>{{ $s->start_date->format('d M Y') }}</td>
                        <td>{{ $s->end_date->format('d M Y') }}</td>
                        <td>{{ $s->location?->name ?? '—' }}</td>
                        <td>{{ $s->leadTrainer?->full_name ?? '—' }}</td>
                        <td>{{ $s->capacity }}</td>
                        <td><span class="badge badge-soft {{ $cls }}">{{ str_replace('_', ' ', $s->status) }}</span></td>
                        <td class="text-end">
                            @can('schedules.update')<a href="{{ route('schedules.edit', $s) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-pencil"></i></a>@endcan
                            @can('schedules.delete')
                                <form method="POST" action="{{ route('schedules.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete this schedule?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-state"><i class="bi bi-calendar-x"></i> No schedules yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
