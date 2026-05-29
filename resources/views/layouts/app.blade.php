@php
    /** @var \App\Services\Navigation\NavigationService $nav */
    $nav = app(\App\Services\Navigation\NavigationService::class);
    $menu = auth()->check() ? $nav->forUser(auth()->user()) : [];

    $initials = function ($name) {
        $parts = preg_split('/\s+/', trim((string) $name));
        return strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'ER Medics') }}</title>

    {{-- Apply saved theme before paint to avoid flash --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('er-theme');
                if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {}
        })();
    </script>

    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<div class="app-shell" id="appShell">

    {{-- Desktop / persistent sidebar --}}
    <aside class="sidebar d-none d-lg-block">
        <div class="brand">
            <a href="{{ route('dashboard') }}" class="d-block">
                <img src="{{ asset('logo.jpeg') }}" alt="ER Medics" style="width:100%;max-width:180px;height:auto;display:block;">
            </a>
        </div>
        @include('layouts.partials.sidebar', ['menu' => $menu])
    </aside>

    <div class="app-main">
        <header class="topbar">
            {{-- Mobile: open offcanvas --}}
            <button type="button" class="btn-icon d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Open menu">
                <i class="bi bi-list"></i>
            </button>
            {{-- Desktop: collapse/expand --}}
            <button type="button" class="btn-icon d-none d-lg-grid" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>

            <div class="search d-none d-md-block">
                <i class="bi bi-search"></i>
                <input type="search" id="globalSearch" placeholder="Search students, courses, certificates..." autocomplete="off">
            </div>

            <div class="ms-auto d-flex align-items-center gap-2">
                <button type="button" class="btn-icon" id="themeToggle" aria-label="Toggle dark mode">
                    <i class="bi bi-moon-stars" id="themeIconDark"></i>
                    <i class="bi bi-sun d-none" id="themeIconLight"></i>
                </button>

                <a href="{{ route('verify.form') }}" class="btn-icon" title="Verify a certificate" target="_blank">
                    <i class="bi bi-shield-check"></i>
                </a>

                @auth
                <div class="dropdown">
                    <a class="user-pill text-decoration-none" data-bs-toggle="dropdown" href="#" role="button">
                        <span class="avatar">{{ $initials(auth()->user()->name) }}</span>
                        <span class="d-none d-sm-inline small fw-semibold">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down small d-none d-sm-inline"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2">
                            <div class="small fw-semibold">{{ auth()->user()->name }}</div>
                            <div class="small text-muted">{{ auth()->user()->email }}</div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>My profile</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Log out</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </header>

        <main class="page-body">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            @hasSection('header')
                <div class="page-header">
                    <div>
                        <h1>@yield('header')</h1>
                        @hasSection('subheader')<div class="subtitle">@yield('subheader')</div>@endif
                    </div>
                    <div class="d-flex flex-wrap gap-2">@yield('header-actions')</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Mobile offcanvas sidebar --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" style="background: var(--er-surface);">
    <div class="offcanvas-header" style="border-bottom: 1px solid var(--er-border);">
        <div class="d-flex align-items-center">
            <img src="{{ asset('logo.jpeg') }}" alt="ER Medics" style="width:120px;height:auto;display:block;">
        </div>
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        @include('layouts.partials.sidebar', ['menu' => $menu])
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Theme toggle
    (function () {
        var html = document.documentElement;
        var btn  = document.getElementById('themeToggle');
        var dark = document.getElementById('themeIconDark');
        var light= document.getElementById('themeIconLight');
        function paint(t) {
            if (t === 'dark') { dark.classList.add('d-none'); light.classList.remove('d-none'); }
            else              { light.classList.add('d-none'); dark.classList.remove('d-none'); }
        }
        paint(html.getAttribute('data-bs-theme') || 'light');
        btn?.addEventListener('click', function () {
            var next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('er-theme', next); } catch (e) {}
            paint(next);
            window.dispatchEvent(new CustomEvent('er:theme', { detail: next }));
        });
    })();

    // Sidebar collapse (desktop)
    (function () {
        var shell = document.getElementById('appShell');
        var btn   = document.getElementById('sidebarToggle');
        try {
            if (localStorage.getItem('er-sidebar') === 'collapsed') shell.classList.add('collapsed');
        } catch (e) {}
        btn?.addEventListener('click', function () {
            shell.classList.toggle('collapsed');
            try {
                localStorage.setItem('er-sidebar', shell.classList.contains('collapsed') ? 'collapsed' : 'open');
            } catch (e) {}
        });
    })();
</script>
@stack('scripts')
</body>
</html>
