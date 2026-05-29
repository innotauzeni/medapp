<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $code): ?Booking;
    public function generateBookingCode(): string;
}
