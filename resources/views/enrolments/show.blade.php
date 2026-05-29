@extends('layouts.app')
@section('title', 'Enrolment')
@section('header', $enrolment->student?->full_name . ' — ' . $enrolment->course?->title)
@section('subheader', 'Enrolment #' . $enrolment->id . ' · ' . $enrolment->status)

@section('header-actions')
    @can('enrolments.update')<a href="{{ route('enrolments.edit', $enrolment) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Edit</a>@endcan
    @can('certificates.create')
        @if (!$enrolment->certificate && $enrolment->status === 'completed')
            <form method="POST" action="{{ route('enrolments.issue-certificate', $enrolment) }}" class="d-inline">
                @csrf
                <button class="btn btn-primary"><i class="bi bi-award me-1"></i> Issue certificate</button>
            </form>
        @endif
    @endcan
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="content-card">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Student</dt><dd class="col-7">{{ $enrolment->student?->full_name }}</dd>
                    <dt class="col-5 text-muted">Course</dt><dd class="col-7">{{ $enrolment->course?->title }}</dd>
                    <dt class="col-5 text-muted">Schedule</dt><dd class="col-7">{{ optional($enrolment->schedule?->start_date)->format('Y-m-d') ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Enrolled</dt><dd class="col-7">{{ optional($enrolment->enrolled_on)->format('Y-m-d') }}</dd>
                    <dt class="col-5 text-muted">Completed</dt><dd class="col-7">{{ optional($enrolment->completed_on)->format('Y-m-d') ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Final score</dt><dd class="col-7">{{ $enrolment->final_score !== null ? number_format($enrolment->final_score, 1) . '%' : '—' }}</dd>
                    <dt class="col-5 text-muted">Status</dt><dd class="col-7"><span class="badge text-bg-info">{{ $enrolment->status }}</span></dd>
                    <dt class="col-5 text-muted">Certificate</dt>
                    <dd class="col-7">
                        @if ($enrolment->certificate)
                            <a href="{{ route('certificates.show', $enrolment->certificate) }}">{{ $enrolment->certificate->certificate_number }}</a>
                        @else
                            <span class="text-muted">Not issued</span>
                        @endif
                    </dd>
                </dl>
            </div>

            @can('enrolments.update')
                @if ($enrolment->status !== 'completed')
                    <div class="content-card mt-3">
                        <h3 class="h6">Mark complete</h3>
                        <form method="POST" action="{{ route('enrolments.complete', $enrolment) }}">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small">Final score (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="final_score" value="{{ $enrolment->final_score }}" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Comments</label>
                                <textarea name="trainer_comments" rows="2" class="form-control form-control-sm">{{ $enrolment->trainer_comments }}</textarea>
                            </div>
                            <button class="btn btn-success btn-sm w-100"><i class="bi bi-check2-circle me-1"></i> Mark complete</button>
                        </form>
                    </div>
                @endif
            @endcan
        </div>

        <div class="col-lg-8">
            @can('enrolments.update')
                <div class="content-card">
                    <h3 class="h6 mb-3">Record attendance</h3>
                    <form method="POST" action="{{ route('enrolments.attendance', $enrolment) }}" class="row g-2">
                        @csrf
                        <div class="col-md-3"><input type="date" name="date" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required></div>
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-sm">
                                @foreach (['present','absent','late','excused'] as $st)<option value="{{ $st }}">{{ $st }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><input name="remarks" class="form-control form-control-sm" placeholder="Remarks"></div>
                        <div class="col-md-2"><button class="btn btn-outline-primary btn-sm w-100">Record</button></div>
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Date</th><th>Status</th><th>Remarks</th></tr></thead>
                            <tbody>
                                @forelse ($enrolment->attendances as $a)
                                    <tr><td>{{ $a->date->format('Y-m-d') }}</td><td>{{ $a->status }}</td><td>{{ $a->remarks }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted text-center small">No attendance recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endcan

            <div class="content-card mt-3">
                <h3 class="h6 mb-3">Assessments</h3>
                @can('enrolments.update')
                    <form method="POST" action="{{ route('enrolments.assessment', $enrolment) }}" class="row g-2 mb-3">
                        @csrf
                        <div class="col-md-3"><input name="title" class="form-control form-control-sm" placeholder="Title *" required></div>
                        <div class="col-md-2">
                            <select name="type" class="form-select form-select-sm">
                                @foreach (['theory','practical','final'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-2"><input type="number" step="0.01" name="score" class="form-control form-control-sm" placeholder="Score"></div>
                        <div class="col-md-2"><input type="number" step="0.01" name="max_score" value="100" class="form-control form-control-sm" placeholder="Max *" required></div>
                        <div class="col-md-2"><input type="date" name="assessed_on" value="{{ now()->toDateString() }}" class="form-control form-control-sm"></div>
                        <div class="col-md-1"><button class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-plus"></i></button></div>
                    </form>
                @endcan
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Title</th><th>Type</th><th>Score</th><th>Outcome</th><th>Date</th></tr></thead>
                        <tbody>
                            @forelse ($enrolment->assessments as $a)
                                <tr>
                                    <td>{{ $a->title }}</td>
                                    <td>{{ $a->type }}</td>
                                    <td>{{ $a->score !== null ? "{$a->score} / {$a->max_score}" : '—' }}</td>
                                    <td>
                                        @if ($a->outcome === 'pass')<span class="badge text-bg-success">Pass</span>
                                        @elseif($a->outcome === 'fail')<span class="badge text-bg-danger">Fail</span>
                                        @else<span class="badge text-bg-secondary">Pending</span>@endif
                                    </td>
                                    <td>{{ optional($a->assessed_on)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center small">No assessments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
