@extends('layouts.public')
@section('title', 'Courses')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between flex-wrap gap-3 align-items-end mb-3">
        <div>
            <h1 class="h3 m-0">All courses</h1>
            <p class="text-muted small mb-0">Pick courses, add to your cart, then book in minutes.</p>
        </div>
        <form method="GET" class="d-flex gap-2" style="max-width: 360px; flex: 1; min-width: 240px;">
            <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Search courses...">
            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    @if ($categories->isNotEmpty())
        <div class="category-pills mb-4">
            <a href="{{ route('public.courses') }}" class="category-pill {{ !$activeCat ? 'active' : '' }}">All categories</a>
            @foreach ($categories as $cat)
                <a href="{{ route('public.courses', ['category' => $cat->slug]) }}"
                   class="category-pill {{ $activeCat === $cat->slug ? 'active' : '' }}">{{ $cat->name }}</a>
            @endforeach
        </div>
    @endif

    <div class="row g-3">
        @forelse ($courses as $c)
            <div class="col-md-6 col-lg-4">
                @include('public.partials.course-card', ['c' => $c])
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-journal-x"></i>
                    No courses match your filters.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
