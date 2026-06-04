@extends('layouts.app')
@section('title', 'Services')
@section('header', 'Services')
@section('subheader', 'Manage the "Our services" grid shown on the landing page')

@section('header-actions')
    @can('services.create')<a href="{{ route('services.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New service</a>@endcan
@endsection

@section('content')
<div class="row g-3">
    @forelse ($paginator as $s)
        <div class="col-md-6 col-lg-4">
            <div class="card-er h-100 d-flex flex-column">
                <div class="card-er-pad flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="icon" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:var(--er-surface-2);">
                            <i class="bi {{ $s->icon }}" style="font-size:1.25rem;"></i>
                        </div>
                        @if ($s->is_active)<span class="badge badge-soft badge-soft-success">Active</span>
                        @else<span class="badge badge-soft badge-soft-muted">Hidden</span>@endif
                    </div>
                    <h3 class="h6 mb-1">{{ $s->title }}</h3>
                    <p class="small text-muted mb-1">{{ \Illuminate\Support\Str::limit($s->description, 120) }}</p>
                    <div class="small text-muted">Order: {{ $s->order_index }}</div>
                </div>
                <div class="card-er-pad d-flex gap-2" style="border-top: 1px solid var(--er-border)">
                    @can('services.update')<a href="{{ route('services.edit', $s) }}" class="btn btn-soft btn-sm flex-grow-1"><i class="bi bi-pencil me-1"></i> Edit</a>@endcan
                    @can('services.delete')
                        <form method="POST" action="{{ route('services.destroy', $s) }}" onsubmit="return confirm('Delete this service?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-soft btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-grid"></i> No services yet. Create one to populate the landing page "Our services" grid.</div>
        </div>
    @endforelse
</div>
<div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
