@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="hero-card mb-4">
        <h1>Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <p>Here's what's happening across your training programmes today.</p>
        <div class="mt-3 d-flex flex-wrap gap-2 position-relative" style="z-index:1;">
            @can('students.create')<a href="{{ route('students.create') }}" class="btn btn-light btn-sm"><i class="bi bi-person-plus me-1"></i> New student</a>@endcan
            @can('enrolments.create')<a href="{{ route('enrolments.create') }}" class="btn btn-light btn-sm"><i class="bi bi-clipboard-plus me-1"></i> Enrol a student</a>@endcan
            @can('certificates.create')<a href="{{ route('certificates.create') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-award me-1"></i> Issue certificate</a>@endcan
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['Active students',  $stats['students'],          'bi-people',         'students.index',     '+'.$deltas['students'].' this month'],
                ['Active courses',   $stats['courses'],           'bi-journal-text',   'courses.index',      'currently running'],
                ['Open enrolments',  $stats['active_enrolments'], 'bi-clipboard-data', 'enrolments.index',   '+'.$deltas['enrolments'].' new this month'],
                ['Certificates',     $stats['certificates'],      'bi-award',          'certificates.index', '+'.$deltas['certificates'].' issued this month'],
            ];
            $iconClass = ['', 'accent', 'success', 'warn'];
        @endphp
        @foreach ($cards as $i => [$label, $value, $icon, $route, $caption])
            <div class="col-md-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon {{ $iconClass[$i] }}"><i class="bi {{ $icon }}"></i></div>
                        @if (\Illuminate\Support\Facades\Route::has($route))
                            <a href="{{ route($route) }}" class="text-muted small text-decoration-none">View →</a>
                        @endif
                    </div>
                    <div class="stat-label">{{ $label }}</div>
                    <div class="stat-value">{{ number_format($value) }}</div>
                    <div class="stat-trend up"><i class="bi bi-arrow-up-right"></i> {{ $caption }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card-er">
                <div class="card-er-head">
                    <div>
                        <h2>Enrolments &amp; certificates</h2>
                        <small class="text-muted">Last 6 months</small>
                    </div>
                    <div class="d-flex gap-3 small">
                        <span><span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:var(--er-primary);"></span> Enrolments</span>
                        <span><span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:var(--er-accent);"></span> Certificates</span>
                    </div>
                </div>
                <div class="card-er-pad">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>

            <div class="card-er mt-3">
                <div class="card-er-head">
                    <h2>Recent enrolments</h2>
                    <a href="{{ route('enrolments.index') }}" class="small text-decoration-none">All enrolments →</a>
                </div>
                <div class="table-responsive">
                    <table class="table-er">
                        <thead><tr><th>Student</th><th>Course</th><th>Status</th><th>Score</th><th>Date</th></tr></thead>
                        <tbody>
                            @forelse ($recentEnrolments as $e)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar-bubble">{{ strtoupper(substr($e->student?->first_name ?? '?',0,1)).strtoupper(substr($e->student?->last_name ?? '',0,1)) }}</span>
                                            <div>
                                                <div class="fw-semibold">{{ $e->student?->full_name }}</div>
                                                <div class="small text-muted">{{ $e->student?->student_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $e->course?->title }}</td>
                                    <td>
                                        @php
                                            $st = $e->status;
                                            $cls = ['enrolled' => 'badge-soft-info', 'in_progress' => 'badge-soft-warn', 'completed' => 'badge-soft-success', 'failed' => 'badge-soft-danger', 'withdrawn' => 'badge-soft-muted'][$st] ?? 'badge-soft-muted';
                                        @endphp
                                        <span class="badge badge-soft {{ $cls }}">{{ str_replace('_', ' ', $st) }}</span>
                                    </td>
                                    <td>{{ $e->final_score !== null ? number_format($e->final_score, 1) . '%' : '—' }}</td>
                                    <td>{{ optional($e->enrolled_on)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-state"><i class="bi bi-inbox"></i> No enrolments yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card-er">
                <div class="card-er-head">
                    <h2>Quick actions</h2>
                </div>
                <div class="card-er-pad d-grid gap-2">
                    @can('students.create')
                        <a href="{{ route('students.create') }}" class="quick-action text-decoration-none">
                            <div class="icon"><i class="bi bi-person-plus"></i></div>
                            <div><div class="label">Register a student</div><div class="desc">Add details, contacts, photo</div></div>
                        </a>
                    @endcan
                    @can('courses.create')
                        <a href="{{ route('courses.create') }}" class="quick-action text-decoration-none">
                            <div class="icon"><i class="bi bi-journal-plus"></i></div>
                            <div><div class="label">Create a course</div><div class="desc">Modules, schedule, trainers</div></div>
                        </a>
                    @endcan
                    @can('certificates.create')
                        <a href="{{ route('certificates.create') }}" class="quick-action text-decoration-none">
                            <div class="icon"><i class="bi bi-award"></i></div>
                            <div><div class="label">Issue certificate</div><div class="desc">Manual or from enrolment</div></div>
                        </a>
                    @endcan
                    <a href="{{ route('verify.form') }}" class="quick-action text-decoration-none" target="_blank">
                        <div class="icon"><i class="bi bi-shield-check"></i></div>
                        <div><div class="label">Verify a certificate</div><div class="desc">Public verification form</div></div>
                    </a>
                    @can('reports.view')
                        <a href="{{ route('reports.index') }}" class="quick-action text-decoration-none">
                            <div class="icon"><i class="bi bi-bar-chart"></i></div>
                            <div><div class="label">View reports</div><div class="desc">Registrations &amp; completions</div></div>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card-er mt-3">
                <div class="card-er-head">
                    <h2>Recently issued certificates</h2>
                    <a href="{{ route('certificates.index') }}" class="small text-decoration-none">All →</a>
                </div>
                <div class="card-er-pad">
                    @forelse ($recentCertificates as $c)
                        <a href="{{ route('certificates.show', $c) }}" class="d-flex align-items-center gap-3 py-2 border-bottom text-decoration-none" style="border-color: var(--er-border) !important;">
                            <div class="stat-icon" style="width:36px;height:36px;font-size:.95rem;"><i class="bi bi-award"></i></div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $c->student?->full_name }}</div>
                                <div class="small text-muted text-truncate">{{ $c->course?->title }}</div>
                            </div>
                            <div class="small text-muted">{{ optional($c->issued_at)->format('M d') }}</div>
                        </a>
                    @empty
                        <div class="empty-state"><i class="bi bi-award"></i> No certificates yet</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const ctx = document.getElementById('trendChart');
        if (!ctx) return;

        const labels       = @json($trend['labels']);
        const enrolments   = @json($trend['enrolments']);
        const certificates = @json($trend['certificates']);

        function colors() {
            const styles = getComputedStyle(document.documentElement);
            return {
                primary: styles.getPropertyValue('--er-primary').trim() || '#c1272d',
                accent:  styles.getPropertyValue('--er-accent').trim()  || '#1f3a8a',
                text:    styles.getPropertyValue('--er-text').trim()    || '#0f172a',
                muted:   styles.getPropertyValue('--er-muted').trim()   || '#6b7280',
                border:  styles.getPropertyValue('--er-border').trim()  || '#e6e9ef',
            };
        }

        let chart;
        function render() {
            const c = colors();
            if (chart) chart.destroy();
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Enrolments',
                            data: enrolments,
                            borderColor: c.primary,
                            backgroundColor: c.primary + '22',
                            fill: true,
                            tension: .35,
                            pointBackgroundColor: c.primary,
                            pointRadius: 4,
                            borderWidth: 2.5,
                        },
                        {
                            label: 'Certificates',
                            data: certificates,
                            borderColor: c.accent,
                            backgroundColor: c.accent + '22',
                            fill: true,
                            tension: .35,
                            pointBackgroundColor: c.accent,
                            pointRadius: 4,
                            borderWidth: 2.5,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: c.muted }, grid: { display: false } },
                        y: { ticks: { color: c.muted, precision: 0 }, grid: { color: c.border } },
                    },
                },
            });
        }

        render();
        window.addEventListener('er:theme', render);
    })();
</script>
@endpush
@endsection
