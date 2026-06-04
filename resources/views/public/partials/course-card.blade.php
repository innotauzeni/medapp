@php
    $img = $c->feature_image_url;
@endphp
<div class="course-card">
    @if ($img)
        <a href="{{ route('public.course', $c) }}" class="course-card-media" style="background-image: url('{{ $img }}');"></a>
    @else
        <a href="{{ route('public.course', $c) }}" class="course-card-media placeholder-media">
            <i class="bi bi-mortarboard"></i>
        </a>
    @endif

    <div class="course-card-body">
        @if ($c->category)
            <div class="mb-2"><span class="badge badge-soft badge-soft-primary">{{ $c->category->name }}</span></div>
        @endif
        <h3 class="h6 mb-1"><a href="{{ route('public.course', $c) }}" class="text-decoration-none" style="color: var(--er-text)">{{ $c->title }}</a></h3>
        <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($c->description, 120) ?: 'Industry-recognised training.' }}</p>
        <div class="meta mb-2">
            <i class="bi bi-clock me-1"></i> {{ $c->duration_hours }} hours
            &nbsp;&middot;&nbsp; <i class="bi bi-patch-check me-1"></i> Pass {{ $c->passing_score }}%
        </div>
        <div class="d-flex justify-content-between align-items-center cta">
            <span class="fee">{{ format_money($c->fee) }}</span>
            <div class="d-flex gap-2">
                <a href="{{ route('public.course', $c) }}" class="btn btn-soft btn-sm">Details</a>
                <form method="POST" action="{{ route('public.cart.add') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $c->id }}">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-cart-plus me-1"></i> Add</button>
                </form>
            </div>
        </div>
    </div>
</div>
