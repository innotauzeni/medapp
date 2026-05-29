<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Location;
use App\Models\Trainer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('schedules.view');
        $filters = $request->only(['status', 'course_id']);
        $query = CourseSchedule::with(['course', 'location', 'leadTrainer'])->orderByDesc('start_date');
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['course_id'])) $query->where('course_id', $filters['course_id']);
        $paginator = $query->paginate(20);
        $courses = Course::orderBy('title')->get(['id', 'code', 'title']);
        return view('schedules.index', compact('paginator', 'filters', 'courses'));
    }

    public function create(): View
    {
        $this->authorize('schedules.create');
        return view('schedules.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('schedules.create');
        $data = $this->validated($request);
        CourseSchedule::create($data);
        return redirect()->route('schedules.index')->with('status', 'Schedule created.');
    }

    public function edit(CourseSchedule $schedule): View
    {
        $this->authorize('schedules.update');
        return view('schedules.edit', array_merge($this->formData(), ['schedule' => $schedule]));
    }

    public function update(Request $request, CourseSchedule $schedule): RedirectResponse
    {
        $this->authorize('schedules.update');
        $schedule->update($this->validated($request));
        return redirect()->route('schedules.index')->with('status', 'Schedule updated.');
    }

    public function destroy(CourseSchedule $schedule): RedirectResponse
    {
        $this->authorize('schedules.delete');
        if ($schedule->enrolments()->exists()) {
            return back()->with('error', 'Cannot delete a schedule with enrolments attached.');
        }
        $schedule->delete();
        return back()->with('status', 'Schedule deleted.');
    }

    private function formData(): array
    {
        return [
            'courses'   => Course::orderBy('title')->get(['id', 'code', 'title']),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'trainers'  => Trainer::where('is_active', true)->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'course_id'       => ['required', 'exists:courses,id'],
            'location_id'     => ['nullable', 'exists:locations,id'],
            'lead_trainer_id' => ['nullable', 'exists:trainers,id'],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
            'capacity'        => ['required', 'integer', 'min:1', 'max:1000'],
            'status'          => ['required', 'in:scheduled,in_progress,completed,cancelled'],
            'notes'           => ['nullable', 'string', 'max:1500'],
        ]);
    }
}
