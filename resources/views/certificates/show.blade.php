@extends('layouts.app')
@section('title', $certificate->certificate_number)
@section('header', 'Certificate')
@section('subheader', $certificate->certificate_number)

@section('header-actions')
    <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-outline-primary" target="_blank"><i class="bi bi-file-pdf me-1"></i> Download PDF</a>
    @can('certificates.delete')
        @if ($certificate->status === 'issued')
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#revokeModal"><i class="bi bi-shield-x me-1"></i> Revoke</button>
        @endif
    @endcan
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="content-card">
                <h2 class="h6 mb-3">Details</h2>
                <dl class="row mb-0 small">
                    <dt class="col-4 text-muted">Number</dt><dd class="col-8"><code>{{ $certificate->certificate_number }}</code></dd>
                    <dt class="col-4 text-muted">Verification code</dt><dd class="col-8"><code>{{ $certificate->verification_code }}</code></dd>
                    <dt class="col-4 text-muted">Student</dt><dd class="col-8">{{ $certificate->student?->full_name }} ({{ $certificate->student?->student_number }})</dd>
                    <dt class="col-4 text-muted">Course</dt><dd class="col-8">{{ $certificate->course?->title }} [{{ $certificate->course?->code }}]</dd>
                    <dt class="col-4 text-muted">Issued at</dt><dd class="col-8">{{ optional($certificate->issued_at)->format('Y-m-d') }}</dd>
                    <dt class="col-4 text-muted">Expires at</dt><dd class="col-8">{{ optional($certificate->expires_at)->format('Y-m-d') ?? '—' }}</dd>
                    <dt class="col-4 text-muted">Score / grade</dt>
                    <dd class="col-8">{{ $certificate->score !== null ? number_format($certificate->score, 1) . '%' : '—' }} · {{ $certificate->grade ?? '—' }}</dd>
                    <dt class="col-4 text-muted">Issued by</dt><dd class="col-8">{{ $certificate->issuer?->name ?? '—' }}</dd>
                    <dt class="col-4 text-muted">Status</dt>
                    <dd class="col-8">
                        @if ($certificate->status === 'issued')<span class="badge text-bg-success">Issued</span>
                        @else<span class="badge text-bg-danger">Revoked</span>@endif
                    </dd>
                    @if ($certificate->status === 'revoked')
                        <dt class="col-4 text-muted">Reason</dt><dd class="col-8">{{ $certificate->revoked_reason ?? '—' }}</dd>
                    @endif
                </dl>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="content-card text-center">
                <h2 class="h6 mb-3">Verification QR</h2>
                @php
                    $verifyUrl = route('verify.show', ['code' => $certificate->verification_code]);
                    $qrSvg = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->margin(0)->generate($verifyUrl));
                @endphp
                <img src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="QR" class="mb-2">
                <div class="small text-muted"><a href="{{ $verifyUrl }}" target="_blank">{{ $verifyUrl }}</a></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="revokeModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('certificates.revoke', $certificate) }}" class="modal-content">
                @csrf @method('PATCH')
                <div class="modal-header"><h5 class="modal-title">Revoke certificate</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" rows="3" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger">Revoke</button>
                </div>
            </form>
        </div>
    </div>
@endsection
