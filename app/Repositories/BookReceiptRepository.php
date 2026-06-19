<?php

namespace App\Repositories;

use App\Models\BookReceipt;
use App\Repositories\Contracts\BookReceiptRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class BookReceiptRepository extends BaseRepository implements BookReceiptRepositoryInterface
{
    protected array $searchable = ['receipt_number'];

    public function __construct(BookReceipt $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['booking_id'])) {
            $query->where('booking_id', $filters['booking_id']);
        }

        return $query->with(['booking', 'payment.paymentChannel'])->latest('id');
    }

    /** Generate the next receipt number: RCP-YY-XXXXXX */
    public function generateReceiptNumber(): string
    {
        $year   = now()->format('y');
        $prefix = "RCP-{$year}-";
        $last   = \App\Models\BookReceipt::where('receipt_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('receipt_number');

        $seq = 1;
        if ($last) {
            $seq = ((int) substr($last, strlen($prefix))) + 1;
        }

        return $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
