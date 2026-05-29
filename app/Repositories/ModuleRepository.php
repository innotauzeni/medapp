<?php

namespace App\Repositories;

use App\Models\AppModule;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ModuleRepository extends BaseRepository implements ModuleRepositoryInterface
{
    public function __construct(AppModule $model)
    {
        parent::__construct($model);
    }

    public function allActiveWithSubmodules(): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->with(['submodules' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('order_index')
            ->get();
    }
}
