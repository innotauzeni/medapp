@extends('layouts.app')
@section('title', 'Course categories')
@section('header', 'Course categories')
@section('subheader', 'Organise courses into categories shown on the public site')

@section('header-actions')
    @can('categories.create')<a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New category</a>@endcan
@endsection

@section('content')
<div class="table-wrap">
    <div class="table-responsive">
        <table class="table-er">
            <thead><tr><th>Name</th><th>Slug</th><th>Courses</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($paginator as $c)
                    <tr>
                        <td class="fw-semibold">{{ $c->name }}</td>
                        <td><code>{{ $c->slug }}</code></td>
                        <td>{{ $c->courses_count }}</td>
                        <td>@if ($c->is_active)<span class="badge badge-soft badge-soft-success">Active</span>@else<span class="badge badge-soft badge-soft-muted">Inactive</span>@endif</td>
                        <td class="text-end">
                            @can('categories.update')<a href="{{ route('categories.edit', $c) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-pencil"></i></a>@endcan
                            @can('categories.delete')
                                <form method="POST" action="{{ route('categories.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state"><i class="bi bi-bookmark-x"></i> No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
