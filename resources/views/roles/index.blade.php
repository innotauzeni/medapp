@extends('layouts.app')
@section('title', 'Roles')
@section('header', 'Roles &amp; permissions')
@section('subheader', 'Control what each role can access and which menus appear')

@section('header-actions')
    @can('roles.create')<a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="bi bi-shield-plus me-1"></i> New role</a>@endcan
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead><tr><th>Role</th><th>Permissions</th><th>Users</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $role->name }}</span>
                                @if (in_array($role->name, $protected, true))
                                    <span class="badge badge-soft badge-soft-muted ms-1" title="Built-in role">Protected</span>
                                @endif
                            </td>
                            <td><span class="badge badge-soft badge-soft-primary">{{ $role->permissions_count }} permission{{ $role->permissions_count === 1 ? '' : 's' }}</span></td>
                            <td>{{ $role->users_count }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @can('roles.update')<a href="{{ route('roles.edit', $role) }}" class="btn btn-soft btn-sm btn-icon-only" title="Edit"><i class="bi bi-pencil"></i></a>@endcan
                                    @can('roles.delete')
                                        @unless (in_array($role->name, $protected, true))
                                            <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Delete the role &quot;{{ $role->name }}&quot;? Users keep their accounts but lose this role.');" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-soft btn-sm btn-icon-only text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endunless
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state"><i class="bi bi-shield-x"></i> No roles defined.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
