<?php

namespace App\Http\Controllers;

use App\Http\Requests\Enrolment\StoreEnrolmentRequest;
use App\Http\Requests\Enrolment\UpdateEnrolmentRequest;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Student;
use App\Services\EnrolmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrolmentController extends Controller
{
    public function __construct(private readonly EnrolmentService $enrolments) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'student_id', 'course_id']);
        $paginator = $this->enrolments->list($filters, $request->integer('per_page', 15) ?: 15);
        return view('enrolments.index', compact('paginator', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('enrolments.create');
        return view('enrolments.create', $this->formData());
    }

    public function store(StoreEnrolmentRequest $request): RedirectResponse
    {
        $e = $this->enrolments->create($request->validated());
        return redirect()->route('enrolments.show', $e)->with('status', 'Enrolment created.');
    }

    public function show(int $enrolment): View
    {
        $this->authorize('enrolments.view');
        $e = $this->enrolments->get($enrolment);
        return view('enrolments.show', ['enrolment' => $e]);
    }

    public function edit(int $enrolment): View
    {
        $this->authorize('enrolments.update');
        $e = $this->enrolments->get($enrolment);
        return view('enrolments.edit', array_merge(['enrolment' => $e], $this->formData()));
    }

    public function update(UpdateEnrolmentRequest $request, int $enrolment): RedirectResponse
    {
        $this->enrolments->update($enrolment, $request->validated());
        return redirect()->route('enrolments.show', $enrolment)->with('status', 'Enrolment updated.');
    }

    public function recordAttendance(Request $request, int $enrolment): RedirectResponse
    {
        $this->authorize('enrolments.update');
        $data = $request->validate([
            'date'    => ['required', 'date'],
            'status'  => ['required', 'in:present,absent,late,excused'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);
        $this->enrolments->recordAttendance($enrolment, $data);
        return back()->with('status', 'Attendance recorded.');
    }

    public function recordAssessment(Request $request, int $enrolment): RedirectResponse
    {
        $this->authorize('enrolments.update');
        $data = $request->validate([
            'course_module_id' => ['nullable', 'exists:course_modules,id'],
            'title'            => ['required', 'string', 'max:200'],
            'type'             => ['required', 'in:theory,practical,final'],
            'score'            => ['nullable', 'numeric', 'min:0'],
            'max_score'        => ['required', 'numeric', 'min:1'],
            'trainer_comments' => ['nullable', 'string', 'max:1500'],
            'assessed_on'      => ['nullable', 'date'],
        ]);
        $this->enrolments->recordAssessment($enrolment, $data);
        return back()->with('status', 'Assessment recorded.');
    }

    public function complete(Request $request, int $enrolment): RedirectResponse
    {
        $this->authorize('enrolments.update');
        $data = $request->validate([
            'final_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'trainer_comments' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->enrolments->complete($enrolment, $data['final_score'] ?? null, $data['trainer_comments'] ?? null);
        return back()->with('status', 'Enrolment marked complete.');
    }

    private function formData(): array
    {
        return [
            'students'  => Student::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'student_number']),
            'courses'   => Course::orderBy('title')->get(['id', 'code', 'title']),
            'schedules' => CourseSchedule::with('course')->orderByDesc('start_date')->get(),
        ];
    }
}
