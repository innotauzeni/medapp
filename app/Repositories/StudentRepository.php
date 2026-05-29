<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    protected array $searchable = ['first_name', 'last_name', 'email', 'phone', 'student_number', 'id_number'];

    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('id');
    }

    public function generateStudentNumber(): string
    {
        $year = now()->format('y');
        $last = Student::withTrashed()
            ->where('student_number', 'like', "ERM-{$year}-%")
            ->orderByDesc('id')
            ->value('student_number');

        $seq = $last ? ((int) substr($last, -5)) + 1 : 1;
        return sprintf('ERM-%s-%05d', $year, $seq);
    }
}
