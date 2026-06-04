<?php

namespace Database\Seeders;

use App\Models\AppModule;
use App\Models\AppSubmodule;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            [
                'key' => 'overview', 'label' => 'Overview', 'icon' => 'bi-speedometer2',
                'order_index' => 10,
                'submodules' => [
                    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-house',     'route_name' => 'dashboard',     'required_permission' => 'dashboard.view', 'order_index' => 10],
                    ['key' => 'reports',   'label' => 'Reports',   'icon' => 'bi-bar-chart', 'route_name' => 'reports.index', 'required_permission' => 'reports.view',   'order_index' => 20],
                ],
            ],
            [
                'key' => 'training', 'label' => 'Training', 'icon' => 'bi-mortarboard',
                'order_index' => 20,
                'submodules' => [
                    ['key' => 'bookings',    'label' => 'Bookings',     'icon' => 'bi-bag-check',       'route_name' => 'bookings.index',     'required_permission' => 'bookings.view',     'order_index' => 5],
                    ['key' => 'students',    'label' => 'Students',     'icon' => 'bi-people',          'route_name' => 'students.index',     'required_permission' => 'students.view',     'order_index' => 10],
                    ['key' => 'enrolments',  'label' => 'Enrolments',   'icon' => 'bi-clipboard-check', 'route_name' => 'enrolments.index',   'required_permission' => 'enrolments.view',   'order_index' => 20],
                    ['key' => 'courses',     'label' => 'Courses',      'icon' => 'bi-journal-text',    'route_name' => 'courses.index',      'required_permission' => 'courses.view',      'order_index' => 30],
                    ['key' => 'categories',  'label' => 'Categories',   'icon' => 'bi-tags',            'route_name' => 'categories.index',   'required_permission' => 'categories.view',   'order_index' => 35],
                    ['key' => 'schedules',   'label' => 'Schedules',    'icon' => 'bi-calendar-event',  'route_name' => 'schedules.index',    'required_permission' => 'schedules.view',    'order_index' => 40],
                    ['key' => 'locations',   'label' => 'Locations',    'icon' => 'bi-geo-alt',         'route_name' => 'locations.index',    'required_permission' => 'locations.view',    'order_index' => 45],
                    ['key' => 'trainers',    'label' => 'Trainers',     'icon' => 'bi-person-badge',    'route_name' => 'trainers.index',     'required_permission' => 'trainers.view',     'order_index' => 50],
                ],
            ],
            [
                'key' => 'awards', 'label' => 'Awards', 'icon' => 'bi-award',
                'order_index' => 30,
                'submodules' => [
                    ['key' => 'certificates', 'label' => 'Certificates', 'icon' => 'bi-award', 'route_name' => 'certificates.index', 'required_permission' => 'certificates.view', 'order_index' => 10],
                ],
            ],
            [
                'key' => 'website', 'label' => 'Website', 'icon' => 'bi-globe',
                'order_index' => 35,
                'submodules' => [
                    ['key' => 'hero_slides', 'label' => 'Hero slides', 'icon' => 'bi-images',     'route_name' => 'hero_slides.index', 'required_permission' => 'hero_slides.view', 'order_index' => 10],
                    ['key' => 'services',    'label' => 'Services',    'icon' => 'bi-grid',      'route_name' => 'services.index',    'required_permission' => 'services.view',    'order_index' => 15],
                    ['key' => 'settings',    'label' => 'Site settings','icon' => 'bi-sliders',    'route_name' => 'settings.edit',     'required_permission' => 'settings.view',    'order_index' => 20],
                ],
            ],
            [
                'key' => 'administration', 'label' => 'Administration', 'icon' => 'bi-gear',
                'required_permission' => null,
                'order_index' => 40,
                'submodules' => [
                    ['key' => 'users', 'label' => 'Users &amp; access',      'icon' => 'bi-shield-lock', 'route_name' => 'users.index', 'required_permission' => 'users.view', 'order_index' => 10],
                    ['key' => 'roles', 'label' => 'Roles &amp; permissions', 'icon' => 'bi-shield-check', 'route_name' => 'roles.index', 'required_permission' => 'roles.view', 'order_index' => 20],
                ],
            ],
        ];

        foreach ($tree as $m) {
            $subs = $m['submodules'] ?? [];
            unset($m['submodules']);
            $module = AppModule::updateOrCreate(['key' => $m['key']], $m + ['is_active' => true]);

            foreach ($subs as $s) {
                AppSubmodule::updateOrCreate(
                    ['app_module_id' => $module->id, 'key' => $s['key']],
                    $s + ['is_active' => true]
                );
            }
        }
    }
}
