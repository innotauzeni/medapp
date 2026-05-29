<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ER Medics') }} — Student Certification &amp; Awards</title>

    <script>
        (function () {
            try {
                var t = localStorage.getItem('er-theme') ||
                    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {}
        })();
    </script>

    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
    <style>
        .hero {
            padding: 80px 0 60px;
            background:
                radial-gradient(800px 500px at 90% -20%, var(--er-primary-50) 0%, transparent 60%),
                radial-gradient(700px 500px at -10% 110%, var(--er-accent-50) 0%, transparent 60%);
        }
        .hero h1 { font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 800; letter-spacing: -.02em; }
        .hero p  { color: var(--er-muted); font-size: 1.05rem; max-width: 640px; margin: 14px auto 0; }
        .feature-card {
            background: var(--er-surface);
            border: 1px solid var(--er-border);
            border-radius: var(--er-radius);
            padding: 24px;
            height: 100%;
            transition: transform .15s, box-shadow .15s;
        }
        .feature-card:hover { transform: translateY(-3px); box-shadow: var(--er-shadow); }
        .feature-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: var(--er-primary-50); color: var(--er-primary-600);
            display: grid; place-items: center; font-size: 1.4rem;
            margin-bottom: 14px;
        }
        .top-nav {
            position: sticky; top: 0; z-index: 10;
            background: var(--er-surface);
            border-bottom: 1px solid var(--er-border);
        }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex align-items-center py-3">
        <span class="d-flex align-items-center gap-2 fw-semibold fs-5">
            <span class="d-inline-grid place-items-center" style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--er-primary),var(--er-primary-600));color:#fff;display:grid;place-items:center;">
                <i class="bi bi-heart-pulse-fill"></i>
            </span>
            <span>ER Medics</span>
        </span>
        <div class="ms-auto d-flex align-items-center gap-2">
            <a href="{{ route('verify.form') }}" class="btn btn-soft btn-sm"><i class="bi bi-shield-check me-1"></i> Verify certificate</a>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Open dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-in-right me-1"></i> Sign in</a>
            @endauth
        </div>
    </div>
</nav>

<header class="hero">
    <div class="container text-center">
        <span class="badge badge-soft badge-soft-primary mb-3">Emergency Medical Training • SHEQ • First Response</span>
        <h1>Student Certification &amp; Awards<br>Management Platform</h1>
        <p>Register students, manage courses and assessments, and issue professional verifiable certificates — all in one place.</p>
        <div class="mt-4 d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg"><i class="bi bi-box-arrow-in-right me-1"></i> Sign in to dashboard</a>
            <a href="{{ route('verify.form') }}" class="btn btn-soft btn-lg"><i class="bi bi-shield-check me-1"></i> Verify a certificate</a>
        </div>
    </div>
</header>

<section class="container py-5">
    <div class="row g-3">
        @php
            $features = [
                ['bi-people',           'Student management',    'Profiles, IDs, contacts, emergency contacts, training history.'],
                ['bi-journal-text',     'Course catalogue',      'Courses, modules, schedules, locations and trainers.'],
                ['bi-clipboard-check',  'Assessments',           'Record results, pass/fail outcomes, trainer comments.'],
                ['bi-award',            'Certificates &amp; awards', 'Automated PDF certificates with QR-code verification.'],
                ['bi-shield-lock',      'Roles &amp; permissions', 'Spatie-powered access control for admins, accounts and customers.'],
                ['bi-bar-chart',        'Reports',               'Registrations, completions, certificates and attendance.'],
            ];
        @endphp
        @foreach ($features as [$icon, $title, $desc])
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi {{ $icon }}"></i></div>
                    <h3 class="h6 mb-1">{!! $title !!}</h3>
                    <p class="text-muted small mb-0">{{ $desc }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<footer class="text-center text-muted small py-4 border-top" style="border-color: var(--er-border) !important;">
    &copy; {{ date('Y') }} ER Medics · Built on Laravel {{ app()->version() }}
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
