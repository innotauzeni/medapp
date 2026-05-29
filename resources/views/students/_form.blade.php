@php $student = $student ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <div class="content-card">
            <h2 class="h6 mb-3">Personal information</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First name *</label>
                    <input name="first_name" value="{{ old('first_name', $student?->first_name) }}" class="form-control @error('first_name') is-invalid @enderror" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last name *</label>
                    <input name="last_name" value="{{ old('last_name', $student?->last_name) }}" class="form-control @error('last_name') is-invalid @enderror" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date of birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($student?->date_of_birth)->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        @foreach (['' => '—', 'male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('gender', $student?->gender) === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ID type *</label>
                    <select name="id_type" class="form-select" required>
                        <option value="id"       @selected(old('id_type', $student?->id_type ?? 'id') === 'id')>ID</option>
                        <option value="passport" @selected(old('id_type', $student?->id_type) === 'passport')>Passport</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">ID / passport number</label>
                    <input name="id_number" value="{{ old('id_number', $student?->id_number) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <input name="nationality" value="{{ old('nationality', $student?->nationality) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profile photo</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                    @if ($student?->profile_photo_path)
                        <img src="{{ asset('storage/'.$student->profile_photo_path) }}" class="mt-2 rounded" style="max-height:80px">
                    @endif
                </div>
            </div>
        </div>

        <div class="content-card mt-3">
            <h2 class="h6 mb-3">Contact &amp; address</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $student?->email) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input name="phone" value="{{ old('phone', $student?->phone) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input name="address_line1" value="{{ old('address_line1', $student?->address_line1) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input name="city" value="{{ old('city', $student?->city) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Province</label>
                    <input name="province" value="{{ old('province', $student?->province) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input name="country" value="{{ old('country', $student?->country ?? 'South Africa') }}" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="content-card">
            <h2 class="h6 mb-3">Emergency contact</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input name="emergency_contact_name" value="{{ old('emergency_contact_name', $student?->emergency_contact_name) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student?->emergency_contact_phone) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Relationship</label>
                    <input name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $student?->emergency_contact_relationship) }}" class="form-control">
                </div>
            </div>
        </div>

        <div class="content-card mt-3">
            <h2 class="h6 mb-3">Employment &amp; notes</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Employer</label>
                    <input name="employer" value="{{ old('employer', $student?->employer) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Job title</label>
                    <input name="job_title" value="{{ old('job_title', $student?->job_title) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $student?->notes) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>
