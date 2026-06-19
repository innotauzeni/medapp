<?php

namespace App\Repositories;

use App\Models\BookPayment;
use App\Repositories\Contracts\BookPaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class BookPaymentRepository extends BaseRepository implements BookPaymentRepositoryInterface
{
    protected array $searchable = ['uuid'];

    public function __construct(BookPayment $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['booking_id'])) {
            $query->where('booking_id', $filters['booking_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with(['booking', 'paymentChannel'])->latest('id');
    }
}

