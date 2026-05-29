<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ModuleRepositoryInterface extends BaseRepositoryInterface
{
    /** All active modules eager-loaded with active submodules. */
    public function allActiveWithSubmodules(): Collection;
}
