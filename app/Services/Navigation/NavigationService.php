<?php

namespace App\Services\Navigation;

use App\Models\User;
use App\Repositories\Contracts\ModuleRepositoryInterface;

class NavigationService
{
    public function __construct(private readonly ModuleRepositoryInterface $modules) {}

    /**
     * Build the sidebar menu for the given user, filtered by permissions.
     * Returns an array of modules; each with `submodules`.
     *
     * @return array<int, array<string, mixed>>
     */
    public function forUser(User $user): array
    {
        $tree = [];

        foreach ($this->modules->allActiveWithSubmodules() as $module) {
            if ($module->required_permission && !$user->can($module->required_permission)) {
                $subs = $module->submodules->filter(
                    fn ($s) => !$s->required_permission || $user->can($s->required_permission)
                );
                if ($subs->isEmpty()) {
                    continue;
                }
            }

            $subs = $module->submodules
                ->filter(fn ($s) => !$s->required_permission || $user->can($s->required_permission))
                ->map(fn ($s) => [
                    'key'        => $s->key,
                    'label'      => $s->label,
                    'icon'       => $s->icon,
                    'route_name' => $s->route_name,
                    'url'        => $this->safeRoute($s->route_name),
                ])->values()->all();

            $tree[] = [
                'key'        => $module->key,
                'label'      => $module->label,
                'icon'       => $module->icon,
                'route_name' => $module->route_name,
                'url'        => $this->safeRoute($module->route_name),
                'submodules' => $subs,
            ];
        }

        return $tree;
    }

    private function safeRoute(?string $name): ?string
    {
        if (!$name) return null;
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : null;
    }
}
