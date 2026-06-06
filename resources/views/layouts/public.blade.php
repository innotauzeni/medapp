@php
    /** @var \App\Services\Cart\CartService $cartSvc */
    $cartSvc   = app(\App\Services\Cart\CartService::class);
    $cartCount = $cartSvc->count();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <!--
    ==============================================
    System Developed By: Innocent Tauzeni
    Company: DaraERP Solutions
    Development Date: 28/05/2026
    Website: https://daraerp.co.zw
    Copyright © 2026 DaraERP Solutions
    ==============================================
    -->

    <meta name="author" content="Innocent Tauzeni - DaraERP Solutions">
    <meta name="creator" content="Innocent Tauzeni">
    <meta name="designer" content="Innocent Tauzeni">
    <meta name="copyright" content="DaraERP Solutions">
    <meta name="developer" content="Innocent Tauzeni">
    <meta name="application-name" content="ER Medics">
    <meta name="generator" content="Developed by Innocent Tauzeni of DaraERP Solutions">
    <title>@yield('title', 'Welcome') · {{ config('app.name', 'ER Medics') }}</title>
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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<nav class="public-nav">
    <div class="container d-flex align-items-center py-3 gap-3">
        <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
            <img src="{{ asset('logo.jpeg') }}" alt="{{ site_setting('site_name', 'ER Medics') }}" style="width:140px;height:auto;display:block;">
        </a>

        <div class="d-none d-lg-flex align-items-center gap-3 ms-3">
            <a href="{{ route('home') }}#about"    class="nav-link-public {{ request()->routeIs('home') ? 'active' : '' }}">About</a>
            <a href="{{ route('home') }}#services" class="nav-link-public">Services</a>
            <a href="{{ route('public.courses') }}" class="nav-link-public {{ request()->routeIs('public.courses') || request()->routeIs('public.course') ? 'active' : '' }}">Courses</a>
            <a href="{{ route('home') }}#contact"  class="nav-link-public">Contact</a>
            <a href="{{ route('track.form') }}"    class="nav-link-public {{ request()->routeIs('track.*') ? 'active' : '' }}">Track booking</a>
            <a href="{{ route('verify.form') }}"   class="nav-link-public">Verify certificate</a>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            {{-- Dark mode temporarily disabled — remove d-none to bring the toggle back. --}}
            <button type="button" class="theme-pill d-none" id="themeTogglePublic" aria-label="Toggle theme" title="Toggle light/dark theme">
                <i class="bi bi-sun-fill pill-icon-bg left"></i>
                <i class="bi bi-moon-stars-fill pill-icon-bg right"></i>
                <span class="pill-knob">
                    <i class="bi bi-sun-fill" id="themeIconDarkPub"></i>
                    <i class="bi bi-moon-stars-fill d-none" id="themeIconLightPub"></i>
                </span>
            </button>
            <a href="{{ route('public.cart') }}" class="btn-icon position-relative" aria-label="Cart">
                <i class="bi bi-cart3"></i>
                <span class="cart-badge" id="cartBadge" style="{{ $cartCount > 0 ? '' : 'display:none;' }}">{{ $cartCount }}</span>
            </a>
            @auth
                <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-soft btn-sm d-none d-md-inline-flex"><i class="bi bi-person me-1"></i> Staff sign in</a>
            @endauth

            <button type="button" class="btn-icon d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#publicNavMobile">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-end" id="publicNavMobile" tabindex="-1" style="background: var(--er-surface);">
    <div class="offcanvas-header"><h5 class="m-0">Menu</h5><button class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body d-flex flex-column gap-2">
        <a href="{{ route('home') }}"            class="nav-link-public">Home</a>
        <a href="{{ route('home') }}#about"      class="nav-link-public">About</a>
        <a href="{{ route('home') }}#services"   class="nav-link-public">Services</a>
        <a href="{{ route('public.courses') }}"  class="nav-link-public">Courses</a>
        <a href="{{ route('home') }}#contact"    class="nav-link-public">Contact</a>
        <a href="{{ route('track.form') }}"      class="nav-link-public">Track booking</a>
        <a href="{{ route('verify.form') }}"     class="nav-link-public">Verify certificate</a>
        <a href="{{ route('public.cart') }}"     class="nav-link-public"><i class="bi bi-cart3 me-1"></i> Cart ({{ $cartCount }})</a>
        @auth
            <a href="{{ url('/dashboard') }}" class="btn btn-primary"><i class="bi bi-speedometer2 me-1"></i> Open dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-soft"><i class="bi bi-person me-1"></i> Staff sign in</a>
        @endauth
    </div>
</div>

@if (session('status'))
    <div class="container mt-3"><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}<button class="btn-close" data-bs-dismiss="alert"></button></div></div>
@endif
@if (session('error'))
    <div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div></div>
@endif

@yield('content')

<footer class="public-footer">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="mb-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="{{ site_setting('site_name', 'ER Medics') }}" style="width:120px;height:auto;display:block;">
                </div>
                <p class="small text-muted mb-0">{{ site_setting('site_tagline', 'Specialised emergency medical training, SHEQ consultancy, and medical equipment supply.') }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-uppercase small text-muted">Explore</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('public.courses') }}" class="text-decoration-none text-muted">All courses</a></li>
                    <li><a href="{{ route('home') }}#services"  class="text-decoration-none text-muted">Services</a></li>
                    <li><a href="{{ route('track.form') }}"     class="text-decoration-none text-muted">Track booking</a></li>
                    <li><a href="{{ route('verify.form') }}"    class="text-decoration-none text-muted">Verify certificate</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase small text-muted">Contact</h6>
                <ul class="list-unstyled small mb-0">
                    @if ($p = site_setting('phone_primary'))   <li><i class="bi bi-telephone me-1 text-muted"></i> {{ $p }}</li>@endif
                    @if ($p2 = site_setting('phone_secondary'))<li><i class="bi bi-telephone me-1 text-muted"></i> {{ $p2 }}</li>@endif
                    @if ($em = site_setting('email'))<li><i class="bi bi-envelope me-1 text-muted"></i> <a href="mailto:{{ $em }}" class="text-muted text-decoration-none">{{ $em }}</a></li>@endif
                </ul>
            </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between small text-muted">
            <span>&copy; {{ date('Y') }} ER Medics. All rights reserved.</span>
            <span>Powered by FS Designs and Technologies </span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Theme toggle (public layout)
    (function () {
        var html = document.documentElement;
        var btn  = document.getElementById('themeTogglePublic');
        var dark = document.getElementById('themeIconDarkPub');
        var light= document.getElementById('themeIconLightPub');
        function paint(t) {
            if (!dark || !light) return;
            if (t === 'dark') { dark.classList.add('d-none'); light.classList.remove('d-none'); }
            else              { light.classList.add('d-none'); dark.classList.remove('d-none'); }
        }
        paint(html.getAttribute('data-bs-theme') || 'light');
        btn?.addEventListener('click', function () {
            var next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('er-theme', next); } catch (e) {}
            paint(next);
        });
    })();

    // Cart <-> localStorage sync
    (function () {
        var server = @json($cartSvc->items());
        try {
            // Write server -> localStorage so cart survives session expiry
            localStorage.setItem('er-cart', JSON.stringify(server));

            // If server lost it but localStorage still has it, send it back
            var keys  = Object.keys(server || {});
            if (keys.length === 0) {
                var local = JSON.parse(localStorage.getItem('er-cart') || '{}');
                var locKeys = Object.keys(local || {});
                if (locKeys.length > 0) {
                    fetch("{{ route('public.cart.sync') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ items: Object.values(local) }),
                    }).then(function (r) {
                        if (r.ok) return r.json();
                    }).then(function (data) {
                        if (data && data.count > 0) {
                            var badge = document.getElementById('cartBadge');
                            if (badge) { badge.textContent = data.count; badge.style.display = ''; }
                        }
                    }).catch(function () {});
                }
            }
        } catch (e) {}
    })();
</script>
@stack('scripts')
    <!-- WhatsApp floating button -->
    <a href="https://wa.me/263784705010?text=For%20more%20information%20contact%20us" target="_blank" rel="noopener" class="btn-whatsapp" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
</body>
</html>
