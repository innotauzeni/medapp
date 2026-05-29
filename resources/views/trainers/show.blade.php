@extends('layouts.app')
@section('title', $trainer->full_name)
@section('header', $trainer->full_name)
@section('subheader', $trainer->specialty ?? 'Trainer')

@section('header-actions')
    @can('trainers.update')<a href="{{ route('trainers.edit', $trainer) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Edit</a>@endcan
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="content-card">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Email</dt><dd class="col-7">{{ $trainer->email ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Phone</dt><dd class="col-7">{{ $trainer->phone ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Specialty</dt><dd class="col-7">{{ $trainer->specialty ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Qualifications</dt><dd class="col-7">{{ $trainer->qualifications ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Active</dt><dd class="col-7">@if ($trainer->is_active)<span class="badge text-bg-success">Yes</span>@else<span class="badge text-bg-secondary">No</span>@endif</dd>
                </dl>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="content-card">
                <h3 class="h6">Bio</h3>
                <p class="mb-0 small">{{ $trainer->bio ?? '—' }}</p>
            </div>
            <div class="content-card mt-3">
                <h3 class="h6 mb-3">Lead schedules</h3>
                @if ($trainer->leadSchedules->isEmpty())
                    <p class="text-muted small mb-0">No assignments.</p>
                @else
                    <ul class="list-unstyled mb-0 small">
                        @foreach ($trainer->leadSchedules as $s)
                            <li>{{ $s->course?->title }} · {{ $s->start_date->format('Y-m-d') }} – {{ $s->end_date->format('Y-m-d') }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
