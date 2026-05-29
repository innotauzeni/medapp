<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function model(): Model
    {
        return $this->model;
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters($this->query(), $filters)->paginate($perPage)->withQueryString();
    }

    public function find(int|string $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function findBy(string $column, mixed $value): ?Model
    {
        return $this->query()->where($column, $value)->first();
    }

    public function create(array $attributes): Model
    {
        return $this->query()->create($attributes);
    }

    public function update(int|string $id, array $attributes): Model
    {
        $record = $this->findOrFail($id);
        $record->fill($attributes)->save();
        return $record->fresh();
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }

    /**
     * Override in child repositories to add filterable columns.
     * Default just applies an `q` search across any column listed in $searchable.
     *
     * @var array<int,string>
     */
    protected array $searchable = [];

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['q']) && !empty($this->searchable)) {
            $q = $filters['q'];
            $query->where(function (Builder $b) use ($q) {
                foreach ($this->searchable as $col) {
                    $b->orWhere($col, 'like', "%{$q}%");
                }
            });
        }

        return $query;
    }
}
