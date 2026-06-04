@extends('layouts.public')
@section('title', $course->title)

@push('styles')
<style>
    /* Course description: comfortable single-column reading, sitting to the
       right of the feature image. */
    .course-prose {
        line-height: 1.7;
        color: var(--er-text);
        text-align: left;
    }
    .course-prose br + br { display: none; } /* collapse blank-line runs */

    /* Feature image sits beside the description (image left, text right) and
       stays anchored at the top of its column. A fixed 4:3 frame keeps the
       padding tight. object-fit:contain shows the WHOLE image (never cropped),
       keeps the original aspect ratio — no stretching, no upscaling — so
       resolution is preserved. */
    .course-hero {
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: var(--er-surface-2);
    }
    .course-hero img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <a href="{{ route('public.courses') }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i> All courses</a>

    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            @if ($course->category)
                <span class="badge badge-soft badge-soft-primary">{{ $course->category->name }}</span>
            @endif
            <h1 class="h2 mt-2">{{ $course->title }}</h1>
            <div class="text-muted small">Course code: <code>{{ $course->code }}</code></div>

            <div class="row g-4 mt-1 align-items-start">
                @if ($course->feature_image_url)
                    <div class="col-md-6">
                        <div class="course-hero rounded">
                            <img src="{{ $course->feature_image_url }}" alt="{{ $course->title }}">
                        </div>
                    </div>
                @endif
                <div class="{{ $course->feature_image_url ? 'col-md-6' : 'col-12' }}">
                    <h2 class="h5">About this course</h2>
                    <div class="course-prose mt-2">{!! nl2br(e($course->description ?? 'Industry-recognised training delivered by experienced ER Medics trainers.')) !!}</div>
                </div>
            </div>

            @if ($course->modules->isNotEmpty())
                <h2 class="h5 mt-4">What you'll cover</h2>
                <ol class="list-group list-group-numbered mt-2">
                    @foreach ($course->modules as $m)
                        <li class="list-group-item d-flex justify-content-between align-items-start" style="background: var(--er-surface); color: var(--er-text); border-color: var(--er-border);">
                            <div>
                                <div class="fw-semibold">{{ $m->title }}</div>
                                @if ($m->description)<div class="small text-muted">{{ $m->description }}</div>@endif
                            </div>
                            <span class="badge badge-soft badge-soft-muted">{{ $m->duration_hours }}h</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-er card-er-pad" style="position: sticky; top: 80px;">
                <div class="text-muted small">Course fee</div>
                <div class="fs-2 fw-bold mb-2">{{ format_money($course->fee) }}</div>
                <div class="d-flex flex-column gap-2 small text-muted mb-3">
                    <div><i class="bi bi-clock me-1"></i> {{ $course->duration_hours }} hours</div>
                    <div><i class="bi bi-patch-check me-1"></i> Pass mark: {{ $course->passing_score }}%</div>
                </div>

                <form method="POST" action="{{ route('public.cart.add') }}">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    @if ($course->schedules->isNotEmpty())
                        <label class="form-label">Pick a schedule (optional)</label>
                        <select name="schedule_id" class="form-select mb-2">
                            <option value="">No specific schedule</option>
                            @foreach ($course->schedules->where('status', 'scheduled')->where('start_date', '>=', now()->toDateString()) as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->start_date->format('d M Y') }}
                                    @if ($s->location) · {{ $s->location->name }} @endif
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" max="50" class="form-control mb-3">

                    <button class="btn btn-primary w-100"><i class="bi bi-cart-plus me-1"></i> Add to cart</button>
                </form>

                <a href="{{ route('public.cart') }}" class="btn btn-soft w-100 mt-2"><i class="bi bi-cart3 me-1"></i> View cart</a>
            </div>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h2 class="h5 mt-5 mb-3">You may also like</h2>
        <div class="row g-3">
            @foreach ($related as $c)
                <div class="col-md-6 col-lg-4">@include('public.partials.course-card', ['c' => $c])</div>
            @endforeach
        </div>
    @endif
</div>
@endsection
