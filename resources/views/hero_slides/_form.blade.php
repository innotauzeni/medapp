@php $slide = $slide ?? null; @endphp
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-er card-er-pad">
            <h2 class="h6 mb-3">Content</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Badge (small caption)</label>
                    <input name="badge" value="{{ old('badge', $slide?->badge) }}" class="form-control" placeholder="e.g. Emergency Medical Training">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Order</label>
                    <input name="order_index" type="number" min="0" value="{{ old('order_index', $slide?->order_index ?? 0) }}" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $slide?->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Title *</label>
                    <input name="title" value="{{ old('title', $slide?->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                    <div class="form-text">You can use <code>&lt;br&gt;</code> to break to a new line.</div>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Subtitle</label>
                    <textarea name="subtitle" rows="3" class="form-control">{{ old('subtitle', $slide?->subtitle) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-er card-er-pad mt-3">
            <h2 class="h6 mb-3">Call to action</h2>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Primary CTA label</label>
                    <input name="cta_label" value="{{ old('cta_label', $slide?->cta_label) }}" class="form-control" placeholder="Browse courses">
                </div>
                <div class="col-md-9">
                    <label class="form-label">Primary CTA URL</label>
                    <input name="cta_url" value="{{ old('cta_url', $slide?->cta_url) }}" class="form-control" placeholder="/courses">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Secondary CTA label</label>
                    <input name="secondary_cta_label" value="{{ old('secondary_cta_label', $slide?->secondary_cta_label) }}" class="form-control" placeholder="Our services">
                </div>
                <div class="col-md-9">
                    <label class="form-label">Secondary CTA URL</label>
                    <input name="secondary_cta_url" value="{{ old('secondary_cta_url', $slide?->secondary_cta_url) }}" class="form-control" placeholder="#services">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-er card-er-pad" style="position: sticky; top: 80px;">
            <h2 class="h6 mb-3">Background image</h2>
            <p class="small text-muted">Either upload a file or paste a public image URL. Recommended size: 1600 × 900.</p>

            @if ($slide && ($src = $slide->imageSrc()))
                <img src="{{ $src }}" alt="" style="width:100%; border-radius:10px; max-height:160px; object-fit:cover;" class="mb-2">
            @endif

            <label class="form-label">Upload image</label>
            <input type="file" name="image_upload" class="form-control mb-2" accept="image/*">

            <label class="form-label mt-2">…or use an image URL</label>
            <input name="image_url" value="{{ old('image_url', $slide?->image_url) }}" class="form-control" placeholder="https://...jpg">

            <label class="form-label mt-3">Background tint (optional)</label>
            <input name="background_color" value="{{ old('background_color', $slide?->background_color) }}" class="form-control" placeholder="#c1272d">
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('hero_slides.index') }}" class="btn btn-soft">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save slide</button>
</div>
