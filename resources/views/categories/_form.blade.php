@php $category = $category ?? null; @endphp
<div class="card-er card-er-pad">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Name *</label>
            <input name="name" value="{{ old('name', $category?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $category?->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $category?->description) }}</textarea>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('categories.index') }}" class="btn btn-soft">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>
