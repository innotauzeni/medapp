@php $trainer = $trainer ?? null; @endphp
<div class="content-card">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">First name *</label>
            <input name="first_name" value="{{ old('first_name', $trainer?->first_name) }}" class="form-control @error('first_name') is-invalid @enderror" required>
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Last name *</label>
            <input name="last_name" value="{{ old('last_name', $trainer?->last_name) }}" class="form-control @error('last_name') is-invalid @enderror" required>
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $trainer?->email) }}" class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input name="phone" value="{{ old('phone', $trainer?->phone) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Specialty</label>
            <input name="specialty" value="{{ old('specialty', $trainer?->specialty) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Qualifications</label>
            <input name="qualifications" value="{{ old('qualifications', $trainer?->qualifications) }}" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Bio</label>
            <textarea name="bio" rows="3" class="form-control">{{ old('bio', $trainer?->bio) }}</textarea>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="trainer_active" name="is_active" value="1" class="form-check-input"
                       @checked(old('is_active', $trainer?->is_active ?? true))>
                <label class="form-check-label" for="trainer_active">Active</label>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('trainers.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>
