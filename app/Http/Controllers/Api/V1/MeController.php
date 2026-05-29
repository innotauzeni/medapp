<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Navigation\NavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __construct(private readonly NavigationService $navigation) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'is_active'  => (bool) $user->is_active,
            ],
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'navigation'  => $this->navigation->forUser($user),
        ]);
    }
}
