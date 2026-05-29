@extends('layouts.app')
@section('title', 'Reports')
@section('header', 'Reports')
@section('subheader', 'Activity since ' . $since->format('Y-m-d'))

@section('header-actions')
    <form method="GET" class="d-inline">
        <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach (['7d' => 'Last 7 days', '30d' => 'Last 30 days', '90d' => 'Last 90 days', '180d' => 'Last 180 days', '365d' => 'Last year'] as $k => $v)
                <option value="{{ $k }}" @selected($range === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </form>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        @foreach ([
            ['Registrations',  $registrations,      'bi-person-plus'],
            ['Completions',    $completions,        'bi-clipboard-check'],
            ['Certificates',   $issued,             'bi-award'],
            ['Total students', $totals['students'], 'bi-people'],
        ] as [$label, $value, $icon])
            <div class="col-md-6 col-lg-3">
                <div class="content-card stat-card h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small">{{ $label }}</div>
                            <div class="fs-3 fw-semibold">{{ number_format($value) }}</div>
                        </div>
                        <i class="bi {{ $icon }} fs-3 text-er-primary"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="content-card">
                <h2 class="h6 mb-3">Top courses by enrolments</h2>
                @if ($byCourse->isEmpty())
                    <p class="text-muted small mb-0">No enrolments in this period.</p>
                @else
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Course</th><th class="text-end">Enrolments</th><th class="text-end">Completed</th></tr></thead>
                        <tbody>
                            @foreach ($byCourse as $row)
                                <tr>
                                    <td>{{ $row->course?->title }} <small class="text-muted">[{{ $row->course?->code }}]</small></td>
                                    <td class="text-end">{{ $row->total }}</td>
                                    <td class="text-end">{{ $row->completed }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="content-card">
                <h2 class="h6 mb-3">Recently issued certificates</h2>
                @if ($latestCertificates->isEmpty())
                    <p class="text-muted small mb-0">No certificates issued in this period.</p>
                @else
                    <table class="table table-sm mb-0">
                        <thead><tr><th>#</th><th>Student</th><th>Course</th><th>Date</th></tr></thead>
                        <tbody>
                            @foreach ($latestCertificates as $c)
                                <tr>
                                    <td><code>{{ $c->certificate_number }}</code></td>
                                    <td>{{ $c->student?->full_name }}</td>
                                    <td>{{ $c->course?->title }}</td>
                                    <td>{{ optional($c->issued_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
