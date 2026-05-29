<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Enrolment;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function __construct(private readonly CertificateRepositoryInterface $certificates) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->certificates->paginate($perPage, $filters);
    }

    public function get(int $id): Certificate
    {
        /** @var Certificate $cert */
        $cert = $this->certificates->findOrFail($id);
        $cert->load(['student', 'course', 'issuer', 'enrolment']);
        return $cert;
    }

    public function issueForEnrolment(Enrolment $enrolment, array $overrides = []): Certificate
    {
        return DB::transaction(function () use ($enrolment, $overrides) {
            if ($enrolment->certificate) {
                return $enrolment->certificate;
            }

            $data = array_merge([
                'certificate_number' => $this->certificates->generateCertificateNumber(),
                'verification_code'  => $this->certificates->generateVerificationCode(),
                'enrolment_id'       => $enrolment->id,
                'student_id'         => $enrolment->student_id,
                'course_id'          => $enrolment->course_id,
                'issued_by'          => Auth::id(),
                'issued_at'          => now()->toDateString(),
                'score'              => $enrolment->final_score,
                'grade'              => $this->grade($enrolment->final_score),
                'status'             => 'issued',
            ], $overrides);

            /** @var Certificate $cert */
            $cert = $this->certificates->create($data);

            if ($enrolment->status !== 'completed') {
                $enrolment->update([
                    'status'       => 'completed',
                    'completed_on' => $enrolment->completed_on ?? now()->toDateString(),
                ]);
            }

            return $cert;
        });
    }

    public function issueManual(array $data): Certificate
    {
        return DB::transaction(function () use ($data) {
            $data['certificate_number'] = $data['certificate_number'] ?? $this->certificates->generateCertificateNumber();
            $data['verification_code']  = $data['verification_code']  ?? $this->certificates->generateVerificationCode();
            $data['issued_by']          = Auth::id();
            $data['issued_at']          = $data['issued_at'] ?? now()->toDateString();
            $data['status']             = 'issued';
            $data['grade']              = $data['grade'] ?? $this->grade($data['score'] ?? null);

            /** @var Certificate $cert */
            $cert = $this->certificates->create($data);
            return $cert;
        });
    }

    public function revoke(int $id, ?string $reason = null): Certificate
    {
        /** @var Certificate $cert */
        $cert = $this->certificates->update($id, [
            'status'         => 'revoked',
            'revoked_reason' => $reason,
        ]);
        return $cert;
    }

    public function verify(string $code, ?Request $request = null): ?Certificate
    {
        $cert = $this->certificates->findByVerificationCode($code)
            ?? $this->certificates->findByNumber($code);

        if ($cert) {
            $cert->verifications()->create([
                'ip_address' => $request?->ip(),
                'user_agent' => substr((string) $request?->userAgent(), 0, 1024),
                'successful' => !$cert->isRevoked() && !$cert->isExpired(),
            ]);
            $cert->load(['student', 'course']);
        }

        return $cert;
    }

    public function buildPdf(Certificate $certificate): \Barryvdh\DomPDF\PDF
    {
        $certificate->loadMissing(['student', 'course', 'issuer']);
        $verifyUrl = route('verify.show', ['code' => $certificate->verification_code]);
        return Pdf::loadView('certificates.pdf.certificate', [
            'certificate' => $certificate,
            'verifyUrl'   => $verifyUrl,
        ])->setPaper('a4', 'landscape');
    }

    public function buildAndStorePdf(Certificate $certificate): string
    {
        $pdf  = $this->buildPdf($certificate);
        $path = "certificates/{$certificate->certificate_number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());
        $certificate->update(['pdf_path' => $path]);
        return $path;
    }

    private function grade(?float $score): ?string
    {
        if ($score === null) return null;
        return match (true) {
            $score >= 85 => 'Distinction',
            $score >= 75 => 'Merit',
            $score >= 50 => 'Pass',
            default       => 'Fail',
        };
    }
}
