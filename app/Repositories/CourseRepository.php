<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    protected array $searchable = ['code', 'title', 'description'];

    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->with('category')->latest('id');
    }
}
