<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Student;
use App\Services\CertificateService;
use App\Services\EnrolmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function __construct(
        private readonly CertificateService $certificates,
        private readonly EnrolmentService $enrolments,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status', 'course_id', 'student_id']);
        $paginator = $this->certificates->list($filters, $request->integer('per_page', 15) ?: 15);
        return view('certificates.index', compact('paginator', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('certificates.create');
        return view('certificates.create', [
            'students' => Student::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'student_number']),
            'courses'  => Course::orderBy('title')->get(['id', 'code', 'title']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('certificates.create');
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_id'  => ['required', 'exists:courses,id'],
            'issued_at'  => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
        $cert = $this->certificates->issueManual($data);
        return redirect()->route('certificates.show', $cert)->with('status', 'Certificate issued.');
    }

    public function issueFromEnrolment(int $enrolment): RedirectResponse
    {
        $this->authorize('certificates.create');
        $e = $this->enrolments->get($enrolment);
        $cert = $this->certificates->issueForEnrolment($e);
        return redirect()->route('certificates.show', $cert)->with('status', 'Certificate issued.');
    }

    public function show(int $certificate): View
    {
        $this->authorize('certificates.view');
        $cert = $this->certificates->get($certificate);
        return view('certificates.show', ['certificate' => $cert]);
    }

    public function download(Certificate $certificate): Response|StreamedResponse
    {
        $this->authorize('certificates.view');
        $pdf = $this->certificates->buildPdf($certificate);
        return $pdf->stream("{$certificate->certificate_number}.pdf");
    }

    public function revoke(Request $request, int $certificate): RedirectResponse
    {
        $this->authorize('certificates.delete');
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $this->certificates->revoke($certificate, $data['reason'] ?? null);
        return back()->with('status', 'Certificate revoked.');
    }
}
