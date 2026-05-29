@extends('layouts.app')
@section('title', 'Enrolments')
@section('header', 'Enrolments')
@section('subheader', 'Student training enrolments, attendance and assessments')

@section('header-actions')
    @can('enrolments.create')<a href="{{ route('enrolments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New enrolment</a>@endcan
@endsection

@section('content')
    <div class="card-er card-er-pad mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach (['enrolled','in_progress','completed','failed','withdrawn'] as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ str_replace('_',' ',$s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead><tr><th>Student</th><th>Course</th><th>Schedule</th><th>Status</th><th>Score</th><th>Enrolled</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($paginator as $e)
                        @php
                            $init = strtoupper(substr($e->student?->first_name ?? '?',0,1) . substr($e->student?->last_name ?? '',0,1));
                            $cls  = ['enrolled' => 'badge-soft-info', 'in_progress' => 'badge-soft-warn', 'completed' => 'badge-soft-success', 'failed' => 'badge-soft-danger', 'withdrawn' => 'badge-soft-muted'][$e->status] ?? 'badge-soft-muted';
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar-bubble">{{ $init }}</span>
                                    <div>
                                        <a href="{{ route('students.show', $e->student) }}" class="text-decoration-none fw-semibold">{{ $e->student?->full_name }}</a>
                                        <div class="small text-muted">{{ $e->student?->student_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('courses.show', $e->course) }}" class="text-decoration-none">{{ $e->course?->title }}</a>
                                <div class="small text-muted">{{ $e->course?->code }}</div>
                            </td>
                            <td>{{ optional($e->schedule?->start_date)->format('Y-m-d') ?? '—' }}</td>
                            <td><span class="badge badge-soft {{ $cls }}">{{ str_replace('_', ' ', $e->status) }}</span></td>
                            <td>{{ $e->final_score !== null ? number_format($e->final_score, 1) . '%' : '—' }}</td>
                            <td>{{ optional($e->enrolled_on)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('enrolments.show', $e) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-eye"></i></a>
                                    @can('enrolments.update')<a href="{{ route('enrolments.edit', $e) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-pencil"></i></a>@endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state"><i class="bi bi-clipboard-x"></i> No enrolments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
