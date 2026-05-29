<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    protected array $searchable = ['booking_code', 'first_name', 'last_name', 'email', 'phone', 'organization'];

    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->with(['items.course', 'items.schedule', 'assignee'])->latest('id');
    }

    public function findByCode(string $code): ?Booking
    {
        return $this->query()
            ->with(['items.course', 'items.schedule', 'statusLogs.user', 'assignee'])
            ->where('booking_code', $code)
            ->first();
    }

    public function generateBookingCode(): string
    {
        $year = now()->format('y');
        do {
            $code = sprintf('ERM-BOOK-%s-%s', $year, Str::upper(Str::random(6)));
        } while (Booking::withTrashed()->where('booking_code', $code)->exists());

        return $code;
    }
}
