<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'role']);
        $paginator = $this->users->list($filters, $request->integer('per_page', 15) ?: 15);
        $roles = Role::orderBy('name')->get();
        return view('users.index', compact('paginator', 'filters', 'roles'));
    }

    public function create(): View
    {
        $this->authorize('users.create');
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('users.create');
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'roles'     => ['nullable', 'array'],
            'roles.*'   => ['string', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = $this->users->create($data, $roles);
        return redirect()->route('users.show', $user)->with('status', 'User created.');
    }

    public function show(int $user): View
    {
        $this->authorize('users.view');
        $u = $this->users->get($user);
        return view('users.show', ['user' => $u]);
    }

    public function edit(int $user): View
    {
        $this->authorize('users.update');
        $u = $this->users->get($user);
        $roles = Role::orderBy('name')->get();
        return view('users.edit', ['user' => $u, 'roles' => $roles]);
    }

    public function update(Request $request, int $user): RedirectResponse
    {
        $this->authorize('users.update');
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'phone'     => ['nullable', 'string', 'max:30'],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles'     => ['nullable', 'array'],
            'roles.*'   => ['string', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        $data['is_active'] = $request->boolean('is_active', true);

        $this->users->update($user, $data, $roles);
        return redirect()->route('users.show', $user)->with('status', 'User updated.');
    }

    public function destroy(int $user): RedirectResponse
    {
        $this->authorize('users.delete');
        $this->users->delete($user);
        return redirect()->route('users.index')->with('status', 'User deleted.');
    }
}
