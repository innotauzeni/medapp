<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Friendly labels for each permission group (the part before the dot).
     * Mirrors the sidebar module labels so admins recognise what a "view"
     * permission unlocks in the menu.
     */
    private const GROUP_LABELS = [
        'dashboard'    => 'Dashboard',
        'reports'      => 'Reports',
        'bookings'     => 'Bookings',
        'students'     => 'Students',
        'enrolments'   => 'Enrolments',
        'courses'      => 'Courses',
        'categories'   => 'Categories',
        'schedules'    => 'Schedules',
        'locations'    => 'Locations',
        'trainers'     => 'Trainers',
        'certificates' => 'Certificates',
        'hero_slides'  => 'Hero slides',
        'settings'     => 'Site settings',
        'users'        => 'Users & access',
        'roles'        => 'Roles & permissions',
        'modules'      => 'Modules',
    ];

    public function __construct(private readonly RoleService $roles) {}

    public function index(): View
    {
        $this->authorize('roles.view');
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();
        return view('roles.index', [
            'roles'     => $roles,
            'protected' => RoleService::PROTECTED_ROLES,
        ]);
    }

    public function create(): View
    {
        $this->authorize('roles.create');
        return view('roles.create', [
            'role'      => null,
            'groups'    => $this->permissionGroups(),
            'assigned'  => [],
            'protected' => false,
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = $this->roles->create($data['name'], $data['permissions'] ?? []);
        return redirect()->route('roles.index')->with('status', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role): View
    {
        $this->authorize('roles.update');
        return view('roles.edit', [
            'role'      => $role,
            'groups'    => $this->permissionGroups(),
            'assigned'  => $role->permissions->pluck('name')->all(),
            'protected' => $this->roles->isProtected($role),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();
        $this->roles->update($role, $data['name'], $data['permissions'] ?? []);
        return redirect()->route('roles.index')->with('status', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('roles.delete');
        $this->roles->delete($role);
        return redirect()->route('roles.index')->with('status', "Role \"{$role->name}\" deleted.");
    }

    /**
     * Permissions grouped by resource for the role form. Each group exposes a
     * friendly label and its individual permissions (e.g. view/create/update).
     *
     * @return array<int, array{key: string, label: string, permissions: array<int, array{name: string, action: string}>}>
     */
    private function permissionGroups(): array
    {
        return Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name, 2)[0])
            ->map(fn ($items, $key) => [
                'key'         => $key,
                'label'       => self::GROUP_LABELS[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                'permissions' => $items->map(fn ($p) => [
                    'name'   => $p->name,
                    'action' => explode('.', $p->name, 2)[1] ?? $p->name,
                ])->values()->all(),
            ])
            ->sortBy('label')
            ->values()
            ->all();
    }
}
