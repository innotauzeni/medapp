<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\CourseCategory;
use App\Services\Ai\GeminiService;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
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

    /**
     * AI-draft a course description and modules from its title, used by the
     * "Write with AI" button on the course form. Returns JSON the front-end
     * inserts into the description field and modules list.
     */
    public function aiGenerate(Request $request, GeminiService $gemini): JsonResponse
    {
        abort_unless($request->user()?->canAny(['courses.create', 'courses.update']), 403);

        $data = $request->validate([
            'title'    => ['required', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:200'],
        ]);

        try {
            $content = $gemini->courseContent($data['title'], $data['category'] ?? null);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($content);
    }
}
