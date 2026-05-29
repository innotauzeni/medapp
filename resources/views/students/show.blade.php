@extends('layouts.app')
@section('title', $student->full_name)
@section('header', $student->full_name)
@section('subheader', 'Student ' . $student->student_number)

@section('header-actions')
    @can('students.update')
        <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Edit</a>
    @endcan
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="content-card text-center">
                @if ($student->profile_photo_path)
                    <img src="{{ asset('storage/'.$student->profile_photo_path) }}" class="rounded-circle mb-2" style="height:120px;width:120px;object-fit:cover">
                @else
                    <div class="rounded-circle bg-light mx-auto mb-2 d-flex align-items-center justify-content-center" style="height:120px;width:120px">
                        <i class="bi bi-person fs-1 text-secondary"></i>
                    </div>
                @endif
                <h3 class="h5 mb-0">{{ $student->full_name }}</h3>
                <div class="small text-muted">{{ $student->email ?? '—' }}</div>
                <div class="small text-muted">{{ $student->phone ?? '' }}</div>
                <hr>
                <dl class="row text-start mb-0 small">
                    <dt class="col-5 text-muted">Number</dt><dd class="col-7"><code>{{ $student->student_number }}</code></dd>
                    <dt class="col-5 text-muted">ID</dt><dd class="col-7">{{ strtoupper($student->id_type) }} · {{ $student->id_number ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Date of birth</dt><dd class="col-7">{{ optional($student->date_of_birth)->format('Y-m-d') ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        @if ($student->status === 'active')<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-secondary">Archived</span>@endif
                    </dd>
                </dl>
            </div>

            <div class="content-card mt-3">
                <h3 class="h6">Emergency contact</h3>
                <div class="small text-muted mb-1">{{ $student->emergency_contact_relationship ?? '—' }}</div>
                <div>{{ $student->emergency_contact_name ?? '—' }}</div>
                <div class="text-muted">{{ $student->emergency_contact_phone ?? '' }}</div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="content-card">
                <h3 class="h6 mb-3">Training history</h3>
                @if ($student->enrolments->isEmpty())
                    <p class="text-muted small mb-0">No enrolments yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>Course</th><th>Status</th><th>Score</th><th>Completed</th><th>Certificate</th></tr></thead>
                            <tbody>
                                @foreach ($student->enrolments as $e)
                                    <tr>
                                        <td>
                                            <a href="{{ route('enrolments.show', $e) }}">{{ $e->course?->title }}</a>
                                            <div class="small text-muted">{{ $e->course?->code }}</div>
                                        </td>
                                        <td><span class="badge text-bg-info">{{ $e->status }}</span></td>
                                        <td>{{ $e->final_score !== null ? number_format($e->final_score, 1) . '%' : '—' }}</td>
                                        <td>{{ optional($e->completed_on)->format('Y-m-d') ?? '—' }}</td>
                                        <td>
                                            @if ($e->certificate)
                                                <a href="{{ route('certificates.show', $e->certificate) }}">{{ $e->certificate->certificate_number }}</a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="content-card mt-3">
                <h3 class="h6">Notes</h3>
                <p class="mb-0 small">{{ $student->notes ?? 'No notes recorded.' }}</p>
            </div>
        </div>
    </div>
@endsection
