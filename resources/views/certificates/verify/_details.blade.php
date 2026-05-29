<dl class="row mt-3 mb-0 small text-start">
    <dt class="col-5 text-muted">Certificate number</dt><dd class="col-7"><code>{{ $certificate->certificate_number }}</code></dd>
    <dt class="col-5 text-muted">Student</dt><dd class="col-7">{{ $certificate->student?->full_name }}</dd>
    <dt class="col-5 text-muted">Course</dt><dd class="col-7">{{ $certificate->course?->title }}</dd>
    <dt class="col-5 text-muted">Issued</dt><dd class="col-7">{{ optional($certificate->issued_at)->format('Y-m-d') }}</dd>
    @if ($certificate->expires_at)
        <dt class="col-5 text-muted">Expires</dt><dd class="col-7">{{ $certificate->expires_at->format('Y-m-d') }}</dd>
    @endif
    @if ($certificate->score !== null)
        <dt class="col-5 text-muted">Score / grade</dt><dd class="col-7">{{ number_format($certificate->score, 1) }}% · {{ $certificate->grade ?? '—' }}</dd>
    @endif
</dl>
