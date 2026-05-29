@extends('layouts.app')
@section('title', 'Hero slides')
@section('header', 'Hero slides')
@section('subheader', 'Manage the carousel shown on the landing page')

@section('header-actions')
    @can('hero_slides.create')<a href="{{ route('hero_slides.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New slide</a>@endcan
@endsection

@section('content')
<div class="row g-3">
    @forelse ($paginator as $s)
        <div class="col-md-6 col-lg-4">
            <div class="card-er">
                <div style="height: 180px; background: var(--er-surface-2); background-image: url('{{ $s->imageSrc() ?? '' }}'); background-size: cover; background-position: center; border-radius: var(--er-radius) var(--er-radius) 0 0;"></div>
                <div class="card-er-pad">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        @if ($s->badge)<span class="badge badge-soft badge-soft-primary">{{ $s->badge }}</span>@endif
                        @if ($s->is_active)<span class="badge badge-soft badge-soft-success">Active</span>
                        @else<span class="badge badge-soft badge-soft-muted">Hidden</span>@endif
                    </div>
                    <h3 class="h6 mb-1">{!! $s->title !!}</h3>
                    <p class="small text-muted">{{ \Illuminate\Support\Str::limit($s->subtitle, 100) }}</p>
                    <div class="small text-muted">Order: {{ $s->order_index }}</div>
                </div>
                <div class="card-er-pad d-flex gap-2" style="border-top: 1px solid var(--er-border)">
                    @can('hero_slides.update')<a href="{{ route('hero_slides.edit', $s) }}" class="btn btn-soft btn-sm flex-grow-1"><i class="bi bi-pencil me-1"></i> Edit</a>@endcan
                    @can('hero_slides.delete')
                        <form method="POST" action="{{ route('hero_slides.destroy', $s) }}" onsubmit="return confirm('Delete this slide?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-soft btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-images"></i> No hero slides yet. Create one to populate the landing page carousel.</div>
        </div>
    @endforelse
</div>
<div class="mt-3 d-flex justify-content-center">{{ $paginator->links() }}</div>
@endsection
