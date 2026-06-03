<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    /**
     * Built-in roles that must never be renamed, deleted, or stripped of
     * permissions. Admin always retains full access so the system can't be
     * locked out.
     *
     * @var array<int, string>
     */
    public const PROTECTED_ROLES = ['Admin'];

    public function create(string $name, array $permissions): Role
    {
        return DB::transaction(function () use ($name, $permissions) {
            $role = Role::create(['name' => $name, 'guard_name' => 'web']);
            $role->syncPermissions($this->sanitize($permissions));
            $this->flushCache();
            return $role;
        });
    }

    public function update(Role $role, string $name, array $permissions): Role
    {
        return DB::transaction(function () use ($role, $name, $permissions) {
            if ($this->isProtected($role)) {
                // Keep the name, force the full permission set — Admin can't be reduced.
                $role->syncPermissions(Permission::pluck('name')->all());
            } else {
                $role->name = $name;
                $role->save();
                $role->syncPermissions($this->sanitize($permissions));
            }
            $this->flushCache();
            return $role->fresh();
        });
    }

    public function delete(Role $role): void
    {
        if ($this->isProtected($role)) {
            abort(403, 'This role is protected and cannot be deleted.');
        }
        $role->delete();
        $this->flushCache();
    }

    public function isProtected(Role $role): bool
    {
        return in_array($role->name, self::PROTECTED_ROLES, true);
    }

    /**
     * Drop any submitted permission names that don't actually exist.
     *
     * @param  array<int, string>  $permissions
     * @return array<int, string>
     */
    private function sanitize(array $permissions): array
    {
        $valid = Permission::pluck('name')->all();
        return array_values(array_intersect($permissions, $valid));
    }

    private function flushCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
