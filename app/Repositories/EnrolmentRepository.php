<?php

namespace App\Repositories;

use App\Models\Enrolment;
use App\Repositories\Contracts\EnrolmentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EnrolmentRepository extends BaseRepository implements EnrolmentRepositoryInterface
{
    public function __construct(Enrolment $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        return $query->with(['student', 'course', 'schedule'])->latest('id');
    }

    public function forStudent(int $studentId): Collection
    {
        return $this->query()->where('student_id', $studentId)
            ->with(['course', 'schedule', 'certificate'])
            ->latest('id')->get();
    }

    public function forCourse(int $courseId): Collection
    {
        return $this->query()->where('course_id', $courseId)
            ->with(['student', 'schedule'])
            ->latest('id')->get();
    }
}
