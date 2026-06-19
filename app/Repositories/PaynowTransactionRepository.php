<?php

namespace App\Repositories;

use App\Models\PaynowTransaction;
use App\Repositories\Contracts\PaynowTransactionRepositoryInterface;

class PaynowTransactionRepository extends BaseRepository implements PaynowTransactionRepositoryInterface
{
    public function __construct(PaynowTransaction $model)
    {
        parent::__construct($model);
    }
}
