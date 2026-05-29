<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EnrolmentRepositoryInterface extends BaseRepositoryInterface
{
    public function forStudent(int $studentId): Collection;
    public function forCourse(int $courseId): Collection;
}
