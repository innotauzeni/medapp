<?php

namespace App\Repositories;

use App\Models\Trainer;
use App\Repositories\Contracts\TrainerRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class TrainerRepository extends BaseRepository implements TrainerRepositoryInterface
{
    protected array $searchable = ['first_name', 'last_name', 'email', 'specialty'];

    public function __construct(Trainer $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->with('user')->latest('id');
    }
}
