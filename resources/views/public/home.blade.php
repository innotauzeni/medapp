@extends('layouts.public')
@section('title', site_setting('site_name', 'ER Medics') . ' â€” Emergency Medical Training, SHEQ &amp; Equipment')

@section('content')

@if ($slides->isNotEmpty())
<section class="hero-carousel">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6500">
        @if ($slides->count() > 1)
            <div class="carousel-indicators">
                @foreach ($slides as $i => $slide)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}"
                            @class(['active' => $i === 0]) aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        @endif

        <div class="carousel-inner">
            @foreach ($slides as $i => $slide)
                @php
                    $bg = $slide->image_url ?: ($slide->image_path ? asset('storage/'.$slide->image_path) : null);
                    $tint = $slide->background_color ?: '#0f172a';
                @endphp
                <div class="carousel-item @if ($i === 0) active @endif">
                    <div class="hero-slide" @if ($bg) style="background-image: linear-gradient(135deg, {{ $tint }}d0 0%, {{ $tint }}80 60%, transparent 100%), url('{{ $bg }}');" @endif>
                        <div class="container">
                            <div class="row align-items-center" style="min-height: 560px;">
                                <div class="col-lg-7">
                                    @if ($slide->badge)
                                        <span class="hero-badge mb-3">{!! $slide->badge !!}</span>
                                    @endif
                                    <h1 class="hero-title">{!! $slide->title !!}</h1>
                                    @if ($slide->subtitle)
                                        <p class="hero-subtitle">{{ $slide->subtitle }}</p>
                                    @endif
                                    <div class="d-flex flex-wrap gap-2 mt-4">
                                        @if ($slide->cta_label)
                                            <a href="{{ $slide->cta_url ?: '#' }}" class="btn btn-light btn-lg fw-semibold">
                                                {{ $slide->cta_label }} <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        @endif
                                        @if ($slide->secondary_cta_label)
                                            <a href="{{ $slide->secondary_cta_url ?: '#' }}" class="btn btn-outline-light btn-lg">
                                                {{ $slide->secondary_cta_label }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($slides->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        @endif
    </div>
</section>
@else
<header class="public-hero">
    <div class="container">
        <h1>{{ site_setting('site_name', 'ER Medics') }}</h1>
        <p>{{ site_setting('site_tagline', 'Emergency Medical Training, SHEQ & Equipment') }}</p>
        <a href="{{ route('public.courses') }}" class="btn btn-primary btn-lg mt-3"><i class="bi bi-mortarboard me-1"></i> Browse courses</a>
    </div>
</header>
@endif

<section class="stats-strip">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-3 col-6 stat-cell">
                <div class="stat-icon-lg"><i class="bi bi-people-fill"></i></div>
                <div class="counter" data-target="{{ $stats['students_trained'] }}">0</div>
                <div class="stat-label-lg">Students registered</div>
            </div>
            <div class="col-md-3 col-6 stat-cell">
                <div class="stat-icon-lg"><i class="bi bi-mortarboard-fill"></i></div>
                <div class="counter" data-target="{{ $stats['courses_offered'] }}">0</div>
                <div class="stat-label-lg">Courses offered</div>
            </div>
            <div class="col-md-3 col-6 stat-cell">
                <div class="stat-icon-lg"><i class="bi bi-award-fill"></i></div>
                <div class="counter" data-target="{{ $stats['certificates_issued'] }}">0</div>
                <div class="stat-label-lg">Certificates issued</div>
            </div>
            <div class="col-md-3 col-6 stat-cell">
                <div class="stat-icon-lg"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="counter" data-target="{{ $stats['completion_rate'] }}" data-suffix="%">0%</div>
                <div class="stat-label-lg">Completion rate</div>
            </div>
        </div>
    </div>
</section>

<section id="about" class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <span class="badge badge-soft badge-soft-primary mb-2">About us</span>
                <h2>Strengthening firstâ€‘response capacity, workplace safety, and community resilience.</h2>
            </div>
            <div class="col-lg-7">
                <p class="lede">ER Medics is a specialised emergency medical training and consultancy organisation. We deliver highâ€‘quality Emergency Medical Courses for First Responders, provide structured mentoring and professional development for paramedics, and design customised SHEQ training and consultancy solutions for organisations across diverse sectors.</p>
                <p class="lede mt-3">A separate arm of our business focuses on the supply of reliable medical and training equipment, ensuring our clients are fully equipped for effective learning, operational readiness, and compliance.</p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="check-pill"><i class="bi bi-check2-circle"></i> Industry-recognised certification</span>
                    <span class="check-pill"><i class="bi bi-check2-circle"></i> Experienced paramedic trainers</span>
                    <span class="check-pill"><i class="bi bi-check2-circle"></i> On-site &amp; classroom delivery</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="services" class="section" style="background: var(--er-surface-2);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge badge-soft badge-soft-primary mb-2">What we do</span>
            <h2>Our services</h2>
            <p class="lede mx-auto">From consulting to equipment to training we cover the full operational readiness lifecycle.</p>
        </div>

        <div class="row g-3">
            <!-- 1. Medical Equipment Consulting -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 0ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Medical Equipment Consulting</h3>
                        <p class="text-muted small mb-0">Assess medical equipment needs, recommend suitable products, and assist with procurement and setup.</p>
                    </div>
                </div>
            </div>

            <!-- 2. Medical Equipment Maintenance -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 60ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Medical Equipment Maintenance</h3>
                        <p class="text-muted small mb-0">Maintenance and servicing packages to keep your medical equipment in optimal condition.</p>
                    </div>
                </div>
            </div>

            <!-- 3. Medical Equipment Rental -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 120ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Medical Equipment Rental</h3>
                        <p class="text-muted small mb-0">Short-term or specialized equipment for events and projects flexible rental services.</p>
                    </div>
                </div>
            </div>

            <!-- 4. Medical Equipment Training -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 180ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Medical Equipment Training</h3>
                        <p class="text-muted small mb-0">Training and support on how to effectively and safely use the medical equipment we supply.</p>
                    </div>
                </div>
            </div>

            <!-- 5. Medical Equipment Customization -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 240ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Medical Equipment Customization</h3>
                        <p class="text-muted small mb-0">Tailoring medical equipment to your specific needs, ensuring optimal performance and usability.</p>
                    </div>
                </div>
            </div>

            <!-- 6. Emergency Preparedness Consultation -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 300ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Emergency Preparedness Consultation</h3>
                        <p class="text-muted small mb-0">Assessments of your facility's emergency preparedness with comprehensive plans and protocols.</p>
                    </div>
                </div>
            </div>

            <!-- 7. Events & VIP Medics Package -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 360ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Events & VIP Medics Package</h3>
                        <p class="text-muted small mb-0">Professional on-site medical coverage for events of any scale, delivered by highly trained clinicians with rapid response capability and discreet VIP support. We safeguard guests, performers, and high-profile clients with seamless, reliable medical readiness.</p>
                    </div>
                </div>
            </div>

            <!-- 8. Emergency Training Programs -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 420ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Emergency Training Programs</h3>
                        <p class="text-muted small mb-0">Comprehensive, competency-based emergency care training designed to build confident responders. Our programs combine practical skills, scenario-based learning, and expert instruction to strengthen workplace safety, preparedness, and compliance.</p>
                    </div>
                </div>
            </div>

            <!-- 9. Safety & Emergency audits -->
            <div class="col-md-6 col-lg-4">
                <div class="service-card" style="--delay: 480ms">
                    <div class="service-card-media" style="background-image: url('https://pub-b68c2443dfde441db9c73ba12d8236b4.r2.dev/courses/ESdrOXZVHqBlaC4Tr4Zz9YNWrn0KjvDeyWUVZM31.png');"></div>
                    <div class="service-card-body">
                        <h3 class="h6 mb-2">Safety & Emergency audits</h3>
                        <p class="text-muted small mb-0">Independent, structured assessments of your emergency systems, equipment, and response capability. We identify gaps, strengthen operational readiness, and ensure your organisation meets safety, regulatory, and best-practice standards.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="courses" class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="badge badge-soft badge-soft-primary mb-2">Train with us</span>
                <h2 class="m-0">Featured courses</h2>
                <p class="lede mt-2 mb-0">Equip yourself or your team with industry-recognised training.</p>
            </div>
            <a href="{{ route('public.courses') }}" class="btn btn-soft">View all courses <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        @if ($categories->isNotEmpty())
            <div class="category-pills mb-4">
                <a href="{{ route('public.courses') }}" class="category-pill active">All</a>
                @foreach ($categories as $cat)
                    <a href="{{ route('public.courses', ['category' => $cat->slug]) }}" class="category-pill">{{ $cat->name }}</a>
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
                        No courses are available right now. Please check back soon.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="cta-strip">
    <div class="container">
        <div class="cta-strip-inner">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <h2 class="m-0">Ready to train your team?</h2>
                    <p class="m-0 mt-2 opacity-75">Browse our catalogue, add courses to your cart, and book in minutes â€” we'll follow up within one business day.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('public.courses') }}" class="btn btn-light btn-lg fw-semibold"><i class="bi bi-cart-plus me-1"></i> Book a course</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact" class="section">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <span class="badge badge-soft badge-soft-primary mb-2">Get in touch</span>
                <h2>Contact {{ site_setting('site_name', 'ER Medics') }}</h2>
                <p class="lede">Call our office for bookings, equipment quotes, or to discuss customised training and SHEQ consulting for your organisation.</p>
                <div class="d-flex flex-wrap gap-3 mt-3">
                    @if ($phone = site_setting('phone_primary'))
                        <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="quick-action text-decoration-none" style="min-width: 220px;">
                            <div class="icon"><i class="bi bi-telephone"></i></div>
                            <div><div class="label">{{ $phone }}</div><div class="desc">Main line</div></div>
                        </a>
                    @endif
                    @if ($phone2 = site_setting('phone_secondary'))
                        <a href="tel:{{ preg_replace('/\D/', '', $phone2) }}" class="quick-action text-decoration-none" style="min-width: 220px;">
                            <div class="icon"><i class="bi bi-telephone"></i></div>
                            <div><div class="label">{{ $phone2 }}</div><div class="desc">Alternate</div></div>
                        </a>
                    @endif
                </div>
                @if ($email = site_setting('email'))
                    <div class="mt-3"><a href="mailto:{{ $email }}" class="text-decoration-none"><i class="bi bi-envelope me-1"></i> {{ $email }}</a></div>
                @endif
            </div>
            <div class="col-lg-6">
                <div class="card-er card-er-pad">
                    <h3 class="h6 mb-3">Ready to enrol?</h3>
                    <p class="small text-muted">Browse our full catalogue, add courses to your cart and complete the booking form online. We'll follow up within one business day.</p>
                    <a href="{{ route('public.courses') }}" class="btn btn-primary"><i class="bi bi-cart-plus me-1"></i> Browse courses</a>
                    <a href="{{ route('track.form') }}" class="btn btn-soft ms-1"><i class="bi bi-search me-1"></i> Track existing booking</a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    (function () {
        const counters = document.querySelectorAll('.counter');
        if (!counters.length) return;
        const animate = (el) => {
            const target = +el.dataset.target || 0;
            const suffix = el.dataset.suffix || '';
            const duration = 1200;
            const startedAt = performance.now();
            (function step(now) {
                const t = Math.min(1, (now - startedAt) / duration);
                const eased = 1 - Math.pow(1 - t, 3);
                el.textContent = Math.round(target * eased).toLocaleString() + suffix;
                if (t < 1) requestAnimationFrame(step);
            })(startedAt);
        };
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) { animate(e.target); io.unobserve(e.target); }
            });
        }, { threshold: 0.4 });
        counters.forEach((c) => io.observe(c));
    })();
</script>
@endpush
@endsection
