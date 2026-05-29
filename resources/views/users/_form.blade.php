@php $user = $user ?? null; @endphp
<div class="content-card">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Name *</label>
            <input name="name" value="{{ old('name', $user?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user?->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input name="phone" value="{{ old('phone', $user?->phone) }}" class="form-control">
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="user_is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $user?->is_active ?? true))>
                <label class="form-check-label" for="user_is_active">Active</label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ $user ? 'Reset password (optional)' : 'Password *' }}</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" {{ $user ? '' : 'required' }}>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>
        <div class="col-12">
            <label class="form-label">Roles</label>
            <div>
                @php $current = $user?->roles->pluck('name')->toArray() ?? old('roles', []); @endphp
                @foreach ($roles as $r)
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="roles[]" value="{{ $r->name }}" id="role-{{ $r->id }}" class="form-check-input" @checked(in_array($r->name, $current))>
                        <label class="form-check-label" for="role-{{ $r->id }}">{{ $r->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>
