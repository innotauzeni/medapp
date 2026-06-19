<?php

namespace App\Repositories;

use App\Models\ManualTransaction;
use App\Repositories\Contracts\ManualTransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ManualTransactionRepository extends BaseRepository implements ManualTransactionRepositoryInterface
{
    protected array $searchable = ["reference"];

    public function __construct(ManualTransaction $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters["booking_id"])) {
            $query->where("booking_id", $filters["booking_id"]);
        }

        return $query->with(["booking", "paymentChannel"])->latest("id");
    }
}
