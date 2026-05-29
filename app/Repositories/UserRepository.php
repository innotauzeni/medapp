<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected array $searchable = ['name', 'email', 'phone'];

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        return $query->with('roles')->latest('id');
    }
}
