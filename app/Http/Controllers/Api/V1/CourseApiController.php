<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CourseApiController extends Controller
{
    public function __construct(private readonly CourseService $courses) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['q', 'category_id', 'is_active']);
        return response()->json($this->courses->list($filters, $request->integer('per_page', 15) ?: 15));
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        return response()->json($this->courses->create($request->validated()), Response::HTTP_CREATED);
    }

    public function show(int $course): JsonResponse
    {
        $this->authorize('courses.view');
        return response()->json($this->courses->get($course));
    }

    public function update(UpdateCourseRequest $request, int $course): JsonResponse
    {
        return response()->json($this->courses->update($course, $request->validated()));
    }

    public function destroy(int $course): JsonResponse
    {
        $this->authorize('courses.delete');
        $this->courses->delete($course);
        return response()->json(['message' => 'Deleted.']);
    }
}
