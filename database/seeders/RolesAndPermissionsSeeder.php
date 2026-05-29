<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $resources = [
            'dashboard'    => ['view'],
            'students'     => ['view', 'create', 'update', 'delete'],
            'courses'      => ['view', 'create', 'update', 'delete'],
            'categories'   => ['view', 'create', 'update', 'delete'],
            'locations'    => ['view', 'create', 'update', 'delete'],
            'schedules'    => ['view', 'create', 'update', 'delete'],
            'trainers'     => ['view', 'create', 'update', 'delete'],
            'enrolments'   => ['view', 'create', 'update', 'delete'],
            'certificates' => ['view', 'create', 'update', 'delete'],
            'bookings'     => ['view', 'update', 'delete'],
            'reports'      => ['view'],
            'users'        => ['view', 'create', 'update', 'delete'],
            'roles'        => ['view', 'create', 'update', 'delete'],
            'modules'      => ['view', 'update'],
            'settings'     => ['view', 'update'],
            'hero_slides'  => ['view', 'create', 'update', 'delete'],
        ];

        $allPermissions = [];
        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                $name = "{$resource}.{$action}";
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                $allPermissions[] = $name;
            }
        }

        $admin    = Role::firstOrCreate(['name' => 'Admin',    'guard_name' => 'web']);
        $account  = Role::firstOrCreate(['name' => 'Account',  'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);

        $admin->syncPermissions($allPermissions);

        $account->syncPermissions([
            'dashboard.view',
            'students.view', 'students.create', 'students.update',
            'courses.view',
            'categories.view',
            'locations.view',
            'schedules.view', 'schedules.create', 'schedules.update',
            'trainers.view',
            'enrolments.view', 'enrolments.create', 'enrolments.update',
            'certificates.view', 'certificates.create',
            'bookings.view', 'bookings.update',
            'reports.view',
        ]);

        $customer->syncPermissions([
            'dashboard.view',
            'certificates.view',
        ]);
    }
}
