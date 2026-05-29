<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentService
{
    public function __construct(private readonly StudentRepositoryInterface $students) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->students->paginate($perPage, $filters);
    }

    public function get(int $id): Student
    {
        /** @var Student $student */
        $student = $this->students->findOrFail($id);
        $student->load(['enrolments.course', 'enrolments.certificate', 'documents']);
        return $student;
    }

    public function create(array $data, ?UploadedFile $photo = null): Student
    {
        return DB::transaction(function () use ($data, $photo) {
            $data['student_number'] = $data['student_number'] ?? $this->students->generateStudentNumber();
            $data['created_by']     = Auth::id();

            if ($photo) {
                $data['profile_photo_path'] = $photo->store('students/photos', 'public');
            }

            /** @var Student $student */
            $student = $this->students->create($data);
            return $student;
        });
    }

    public function update(int $id, array $data, ?UploadedFile $photo = null): Student
    {
        return DB::transaction(function () use ($id, $data, $photo) {
            if ($photo) {
                $existing = $this->students->findOrFail($id);
                if ($existing->profile_photo_path) {
                    Storage::disk('public')->delete($existing->profile_photo_path);
                }
                $data['profile_photo_path'] = $photo->store('students/photos', 'public');
            }
            /** @var Student $student */
            $student = $this->students->update($id, $data);
            return $student;
        });
    }

    public function archive(int $id): bool
    {
        $this->students->update($id, ['status' => 'archived']);
        return true;
    }

    public function restore(int $id): bool
    {
        $this->students->update($id, ['status' => 'active']);
        return true;
    }

    public function delete(int $id): bool
    {
        return $this->students->delete($id);
    }
}
