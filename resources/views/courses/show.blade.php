@extends('layouts.app')
@section('title', $course->title)
@section('header', $course->title)
@section('subheader', 'Course ' . $course->code)

@section('header-actions')
    @can('courses.update')<a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Edit</a>@endcan
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="content-card">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Code</dt><dd class="col-7"><code>{{ $course->code }}</code></dd>
                    <dt class="col-5 text-muted">Category</dt><dd class="col-7">{{ $course->category?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Duration</dt><dd class="col-7">{{ $course->duration_hours }} hours</dd>
                    <dt class="col-5 text-muted">Passing score</dt><dd class="col-7">{{ $course->passing_score }}%</dd>
                    <dt class="col-5 text-muted">Fee</dt><dd class="col-7">{{ format_money($course->fee) }}</dd>
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">@if ($course->is_active)<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-secondary">Inactive</span>@endif</dd>
                </dl>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="content-card">
                <h3 class="h6">Description</h3>
                <p class="mb-0 small">{{ $course->description ?? '—' }}</p>
            </div>

            <div class="content-card mt-3">
                <h3 class="h6 mb-3">Modules</h3>
                @if ($course->modules->isEmpty())
                    <p class="text-muted small mb-0">No modules configured.</p>
                @else
                    <ol class="list-group list-group-numbered">
                        @foreach ($course->modules as $m)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $m->title }}</div>
                                    <div class="small text-muted">{{ $m->description }}</div>
                                </div>
                                <span class="badge text-bg-light">{{ $m->duration_hours }}h</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            <div class="content-card mt-3">
                <h3 class="h6 mb-3">Schedules</h3>
                @if ($course->schedules->isEmpty())
                    <p class="text-muted small mb-0">No schedules.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Start</th><th>End</th><th>Location</th><th>Lead trainer</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach ($course->schedules as $s)
                                    <tr>
                                        <td>{{ $s->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $s->end_date->format('Y-m-d') }}</td>
                                        <td>{{ $s->location?->name ?? '—' }}</td>
                                        <td>{{ $s->leadTrainer?->full_name ?? '—' }}</td>
                                        <td><span class="badge text-bg-info">{{ $s->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
