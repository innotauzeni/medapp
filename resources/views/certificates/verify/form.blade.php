<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Certificate · {{ config('app.name', 'ER Medics') }}</title>
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
</head>
<body>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <img src="{{ asset('logo.jpeg') }}" alt="ER Medics" style="width:180px;height:auto;display:block;margin:0 auto;">
        </div>

        <p class="small text-muted text-center mb-4">Enter the verification code or certificate number printed on the certificate.</p>

        <form method="POST" action="{{ route('verify.submit') }}">
            @csrf
            <div class="mb-3">
                <input name="code" class="form-control form-control-lg text-center fw-semibold" style="letter-spacing:.05em;" placeholder="e.g. ERM-CERT-2026-000001" required autofocus>
            </div>
            <button class="btn btn-primary w-100 py-2"><i class="bi bi-shield-check me-1"></i> Verify certificate</button>
        </form>

        <div class="text-center mt-4 small">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none">← Back to homepage</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
