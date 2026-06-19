<?php

namespace App\Repositories\Contracts;

use App\Models\PaymentChannel;

interface PaymentChannelRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?PaymentChannel;

    public function findByName(string $name): ?PaymentChannel;
}
