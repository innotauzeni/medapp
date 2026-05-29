<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\CourseCategory;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private readonly CourseService $courses) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'category_id', 'is_active']);
        $paginator = $this->courses->list($filters, $request->integer('per_page', 15) ?: 15);
        $categories = CourseCategory::orderBy('name')->get();
        return view('courses.index', compact('paginator', 'filters', 'categories'));
    }

    public function create(): View
    {
        $this->authorize('courses.create');
        $categories = CourseCategory::orderBy('name')->get();
        return view('courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = $this->courses->create($request->validated());
        return redirect()->route('courses.show', $course)->with('status', 'Course created.');
    }

    public function show(int $course): View
    {
        $this->authorize('courses.view');
        $courseModel = $this->courses->get($course);
        return view('courses.show', ['course' => $courseModel]);
    }

    public function edit(int $course): View
    {
        $this->authorize('courses.update');
        $courseModel = $this->courses->get($course);
        $categories = CourseCategory::orderBy('name')->get();
        return view('courses.edit', ['course' => $courseModel, 'categories' => $categories]);
    }

    public function update(UpdateCourseRequest $request, int $course): RedirectResponse
    {
        $this->courses->update($course, $request->validated());
        return redirect()->route('courses.show', $course)->with('status', 'Course updated.');
    }

    public function destroy(int $course): RedirectResponse
    {
        $this->authorize('courses.delete');
        $this->courses->delete($course);
        return redirect()->route('courses.index')->with('status', 'Course deleted.');
    }
}
