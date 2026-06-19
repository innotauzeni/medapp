<?php

namespace App\Repositories;

use App\Models\PaymentChannel;
use App\Repositories\Contracts\PaymentChannelRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class PaymentChannelRepository extends BaseRepository implements PaymentChannelRepositoryInterface
{
    protected array $searchable = ["name", "slug"];

    public function __construct(PaymentChannel $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (isset($filters["is_active"])) {
            $query->where("is_active", (bool) $filters["is_active"]);
        }

        return $query->latest("id");
    }

    public function findBySlug(string $slug): ?PaymentChannel
    {
        return $this->query()->where("slug", $slug)->first();
    }

    public function findByName(string $name): ?PaymentChannel
    {
        return $this->query()->where("name", $name)->first();
    }
}
