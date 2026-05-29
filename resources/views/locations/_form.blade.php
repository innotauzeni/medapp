@php $location = $location ?? null; @endphp
<div class="card-er card-er-pad">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Name *</label>
            <input name="name" value="{{ old('name', $location?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Contact phone</label>
            <input name="contact_phone" value="{{ old('contact_phone', $location?->contact_phone) }}" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Address line</label>
            <input name="address_line1" value="{{ old('address_line1', $location?->address_line1) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">City</label>
            <input name="city" value="{{ old('city', $location?->city) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Province</label>
            <input name="province" value="{{ old('province', $location?->province) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Country</label>
            <input name="country" value="{{ old('country', $location?->country ?? 'South Africa') }}" class="form-control">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $location?->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('locations.index') }}" class="btn btn-soft">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>
