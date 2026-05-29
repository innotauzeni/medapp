<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateApiController extends Controller
{
    public function __construct(private readonly CertificateService $certificates) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['q', 'status', 'course_id', 'student_id']);
        return response()->json($this->certificates->list($filters, $request->integer('per_page', 15) ?: 15));
    }

    public function show(int $certificate): JsonResponse
    {
        $this->authorize('certificates.view');
        return response()->json($this->certificates->get($certificate));
    }

    public function verify(Request $request, string $code): JsonResponse
    {
        $cert = $this->certificates->verify($code, $request);
        if (!$cert) {
            return response()->json(['valid' => false, 'reason' => 'not_found'], 404);
        }
        return response()->json([
            'valid'   => !$cert->isRevoked() && !$cert->isExpired(),
            'status'  => $cert->status,
            'expired' => $cert->isExpired(),
            'revoked' => $cert->isRevoked(),
            'certificate' => [
                'certificate_number' => $cert->certificate_number,
                'student'            => $cert->student?->full_name,
                'course'             => $cert->course?->title,
                'issued_at'          => optional($cert->issued_at)?->toDateString(),
                'expires_at'         => optional($cert->expires_at)?->toDateString(),
                'score'              => $cert->score,
                'grade'              => $cert->grade,
            ],
        ]);
    }
}
