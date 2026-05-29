<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->users->paginate($perPage, $filters);
    }

    public function get(int $id): User
    {
        /** @var User $user */
        $user = $this->users->findOrFail($id);
        $user->load(['roles', 'permissions']);
        return $user;
    }

    public function create(array $data, array $roles = []): User
    {
        return DB::transaction(function () use ($data, $roles) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            /** @var User $user */
            $user = $this->users->create($data);
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }
            return $user->load('roles');
        });
    }

    public function update(int $id, array $data, ?array $roles = null): User
    {
        return DB::transaction(function () use ($id, $data, $roles) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            /** @var User $user */
            $user = $this->users->update($id, $data);
            if ($roles !== null) {
                $user->syncRoles($roles);
            }
            return $user->load('roles');
        });
    }

    public function delete(int $id): bool
    {
        return $this->users->delete($id);
    }
}
