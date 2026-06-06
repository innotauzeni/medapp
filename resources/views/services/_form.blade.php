@php $service = $service ?? null; @endphp
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-er card-er-pad">
            <h2 class="h6 mb-3">Content</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Icon (Bootstrap Icons class)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi {{ old('icon', $service?->icon ?? 'bi-clipboard-data') }}"></i></span>
                        <input name="icon" value="{{ old('icon', $service?->icon ?? 'bi-clipboard-data') }}" class="form-control @error('icon') is-invalid @enderror">
                    </div>
                    <div class="form-text">e.g. <code>bi-tools</code>, <code>bi-box-seam</code>. Browse names at <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a>.</div>
                    @error('icon')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Order</label>
                    <input name="order_index" type="number" min="0" value="{{ old('order_index', $service?->order_index ?? 0) }}" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $service?->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Title *</label>
                    <input name="title" value="{{ old('title', $service?->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $service?->description) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-er card-er-pad">
            <h2 class="h6 mb-3">Image</h2>
            @if ($service?->feature_image_url)
                <div class="mb-3 d-flex align-items-center gap-3">
                    <img src="{{ $service->feature_image_url }}" alt="Current image"
                         style="height:72px;width:auto;border-radius:.5rem;object-fit:cover;">
                    <div class="form-check mb-0">
                        <input type="checkbox" id="remove_image" name="remove_image" value="1" class="form-check-input">
                        <label class="form-check-label small" for="remove_image">Remove current image</label>
                    </div>
                </div>
            @endif
            <label class="form-label">Upload image</label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   class="form-control @error('image') is-invalid @enderror">
            <div class="form-text">JPG, PNG or WebP, up to 4&nbsp;MB.</div>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('services.index') }}" class="btn btn-soft">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save service</button>
</div>