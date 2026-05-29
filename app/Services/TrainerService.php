<?php

namespace App\Services;

use App\Models\Trainer;
use App\Repositories\Contracts\TrainerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrainerService
{
    public function __construct(private readonly TrainerRepositoryInterface $trainers) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->trainers->paginate($perPage, $filters);
    }

    public function get(int $id): Trainer
    {
        /** @var Trainer $trainer */
        $trainer = $this->trainers->findOrFail($id);
        $trainer->load(['user', 'schedules.course', 'leadSchedules.course']);
        return $trainer;
    }

    public function create(array $data): Trainer
    {
        /** @var Trainer $t */
        $t = $this->trainers->create($data);
        return $t;
    }

    public function update(int $id, array $data): Trainer
    {
        /** @var Trainer $t */
        $t = $this->trainers->update($id, $data);
        return $t;
    }

    public function delete(int $id): bool
    {
        return $this->trainers->delete($id);
    }
}
