<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verification result · {{ config('app.name', 'ER Medics') }}</title>
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
    <div class="verify-result">
        @if (!$certificate)
            <div class="verify-icon danger"><i class="bi bi-x-octagon-fill"></i></div>
            <h1 class="h4 mb-1">No certificate found</h1>
            <p class="text-muted small">No record matches the code <code>{{ $code }}</code>.</p>
        @elseif ($certificate->isRevoked())
            <div class="verify-icon danger"><i class="bi bi-shield-x"></i></div>
            <h1 class="h4 mb-1">Certificate revoked</h1>
            <p class="text-muted small">{{ $certificate->revoked_reason ?? 'This certificate has been revoked by ER Medics.' }}</p>
            @include('certificates.verify._details', ['certificate' => $certificate])
        @elseif ($certificate->isExpired())
            <div class="verify-icon warn"><i class="bi bi-clock-history"></i></div>
            <h1 class="h4 mb-1">Certificate expired</h1>
            <p class="text-muted small">This certificate is valid but expired on {{ $certificate->expires_at->format('Y-m-d') }}.</p>
            @include('certificates.verify._details', ['certificate' => $certificate])
        @else
            <div class="verify-icon ok"><i class="bi bi-patch-check-fill"></i></div>
            <h1 class="h4 mb-1">Verified ✓ Certificate is valid</h1>
            <p class="text-muted small">Issued by ER Medics.</p>
            @include('certificates.verify._details', ['certificate' => $certificate])
        @endif

        <div class="mt-4">
            <a href="{{ route('verify.form') }}" class="btn btn-soft btn-sm">← Verify another</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
