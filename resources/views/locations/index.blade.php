@extends('layouts.app')
@section('title', 'Training locations')
@section('header', 'Training locations')
@section('subheader', 'Venues where courses can be delivered')

@section('header-actions')
    @can('locations.create')<a href="{{ route('locations.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New location</a>@endcan
@endsection

@section('content')
<div class="table-wrap">
    <div class="table-responsive">
        <table class="table-er">
            <thead><tr><th>Name</th><th>City</th><th>Province</th><th>Phone</th><th>Schedules</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($paginator as $l)
                    <tr>
                        <td class="fw-semibold">{{ $l->name }}<div class="small text-muted">{{ $l->address_line1 }}</div></td>
                        <td>{{ $l->city ?? '—' }}</td>
                        <td>{{ $l->province ?? '—' }}</td>
                        <td>{{ $l->contact_phone ?? '—' }}</td>
                        <td>{{ $l->schedules_count }}</td>
                        <td>@if ($l->is_active)<span class="badge badge-soft badge-soft-success">Active</span>@else<span class="badge badge-soft badge-soft-muted">Inactive</span>@endif</td>
                        <td class="text-end">
                            @can('locations.update')<a href="{{ route('locations.edit', $l) }}" class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-pencil"></i></a>@endcan
                            @can('locations.delete')
                                <form method="POST" action="{{ route('locations.destroy', $l) }}" class="d-inline" onsubmit="return confirm('Delete this location?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-soft btn-sm btn-icon-only"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-geo-alt"></i> No locations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
