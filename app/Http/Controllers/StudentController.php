<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(private readonly StudentService $students) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status']);
        $paginator = $this->students->list($filters, $request->integer('per_page', 15) ?: 15);
        return view('students.index', compact('paginator', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('students.create');
        return view('students.create');
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $student = $this->students->create($request->validated(), $request->file('profile_photo'));
        return redirect()->route('students.show', $student)->with('status', 'Student created.');
    }

    public function show(int $student): View
    {
        $this->authorize('students.view');
        $studentModel = $this->students->get($student);
        return view('students.show', ['student' => $studentModel]);
    }

    public function edit(int $student): View
    {
        $this->authorize('students.update');
        $studentModel = $this->students->get($student);
        return view('students.edit', ['student' => $studentModel]);
    }

    public function update(UpdateStudentRequest $request, int $student): RedirectResponse
    {
        $this->students->update($student, $request->validated(), $request->file('profile_photo'));
        return redirect()->route('students.show', $student)->with('status', 'Student updated.');
    }

    public function destroy(int $student): RedirectResponse
    {
        $this->authorize('students.delete');
        $this->students->delete($student);
        return redirect()->route('students.index')->with('status', 'Student deleted.');
    }

    public function archive(int $student): RedirectResponse
    {
        $this->authorize('students.update');
        $this->students->archive($student);
        return back()->with('status', 'Student archived.');
    }

    public function restore(int $student): RedirectResponse
    {
        $this->authorize('students.update');
        $this->students->restore($student);
        return back()->with('status', 'Student restored.');
    }
}
