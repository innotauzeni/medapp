@extends('layouts.app')
@section('title', 'Courses')
@section('header', 'Courses')
@section('subheader', 'Emergency medical and SHEQ training catalogue')

@section('header-actions')
    @can('courses.create')<a href="{{ route('courses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New course</a>@endcan
@endsection

@section('content')
    <div class="card-er card-er-pad mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Code, title, description...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((int)($filters['category_id'] ?? 0) === $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">Any</option>
                    <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Active</option>
                    <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table-er">
                <thead>
                    <tr><th>Code</th><th>Title</th><th>Category</th><th>Duration</th><th>Pass</th><th>Fee</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($paginator as $c)
                        <tr>
                            <td><code>{{ $c->code }}</code></td>
                            <td><a href="{{ route('courses.show', $c) }}" class="text-decoration-none fw-semibold">{{ $c->title }}</a></td>
                            <td>{{ $c->category?->name ? : '—' }}</td>
                            <td>{{ $c->duration_hours }} h</td>
                            <td>{{ $c->passing_score }}%</td>
                            <td>{{ format_money($c->fee) }}</td>
                            <td>
                                @if ($c->is_active)
                                    <span class="badge badge-soft badge-soft-success">Active</span>
                                @else
                                    <span class="badge badge-soft badge-soft-muted">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('courses.show', $c) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-eye"></i></a>
                                    @can('courses.update')<a href="{{ route('courses.edit', $c) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-pencil"></i></a>@endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state"><i class="bi bi-journal-x"></i> No courses found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
