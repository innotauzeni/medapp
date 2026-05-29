<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class StudentApiController extends Controller
{
    public function __construct(private readonly StudentService $students) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['q', 'status']);
        $paginator = $this->students->list($filters, $request->integer('per_page', 15) ?: 15);
        return response()->json($paginator);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->students->create($request->validated(), $request->file('profile_photo'));
        return response()->json($student, Response::HTTP_CREATED);
    }

    public function show(int $student): JsonResponse
    {
        $this->authorize('students.view');
        return response()->json($this->students->get($student));
    }

    public function update(UpdateStudentRequest $request, int $student): JsonResponse
    {
        $s = $this->students->update($student, $request->validated(), $request->file('profile_photo'));
        return response()->json($s);
    }

    public function destroy(int $student): JsonResponse
    {
        $this->authorize('students.delete');
        $this->students->delete($student);
        return response()->json(['message' => 'Deleted.']);
    }
}
