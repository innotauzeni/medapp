<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PaynowRepositoryInterface
{
    public function gettransactions(int $booking_id): Collection;

    public function initiatetransaction(array $data): array;

    public function checktransaction(string $uuid): array;

    public function checktransactionbyid(int $id): array;
}
