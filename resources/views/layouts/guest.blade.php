<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') · {{ config('app.name', 'ER Medics') }}</title>

    {{-- Dark mode temporarily disabled — force light. Re-enable by restoring the block below. --}}
    <script>
        document.documentElement.setAttribute('data-bs-theme', 'light');
        {{--
        (function () {
            try {
                var t = localStorage.getItem('er-theme') ||
                    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {}
        })();
        --}}
    </script>

    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
</head>
<body>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <img src="{{ asset('logo.jpeg') }}" alt="ER Medics" style="width:180px;height:auto;display:block;margin:0 auto;">
        </div>

        {{ $slot ?? '' }}
        @yield('content')

        {{-- Dark mode temporarily disabled — remove d-none to bring the toggle back. --}}
        <div class="text-center mt-4 small text-muted d-none">
            <button type="button" id="themeToggle" class="btn btn-sm btn-soft">
                <i class="bi bi-moon-stars me-1"></i> Toggle theme
            </button>
        </div>
    </div>
    <div class="text-center text-muted small mt-3">&copy; {{ date('Y') }} ER Medics</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('themeToggle')?.addEventListener('click', function () {
        var html = document.documentElement;
        var next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-bs-theme', next);
        try { localStorage.setItem('er-theme', next); } catch (e) {}
    });
</script>
</body>
</html>
