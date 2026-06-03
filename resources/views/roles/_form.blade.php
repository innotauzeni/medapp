@php
    $role      = $role ?? null;
    $protected = $protected ?? false;
    $checked   = old('permissions', $assigned ?? []);
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@if ($protected)
    <div class="alert alert-info d-flex align-items-center gap-2">
        <i class="bi bi-shield-lock"></i>
        <div>This is a built-in role with full access. Its name and permissions can't be changed.</div>
    </div>
@endif

<div class="content-card">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Role name *</label>
            <input name="name" value="{{ old('name', $role?->name) }}"
                   class="form-control @error('name') is-invalid @enderror"
                   {{ $protected ? 'readonly' : 'required' }} maxlength="100" placeholder="e.g. Front desk, Trainer, Finance">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="content-card mt-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="h6 mb-1">Permissions</h2>
            <p class="text-muted small mb-0">
                Granting <strong>view</strong> on a section makes its menu appear in the sidebar for this role.
            </p>
        </div>
        @unless ($protected)
            <button type="button" class="btn btn-soft btn-sm" id="toggle-all"><i class="bi bi-check2-square me-1"></i> Select all</button>
        @endunless
    </div>

    <div class="row g-3">
        @foreach ($groups as $group)
            <div class="col-md-6 col-xl-4">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold small text-uppercase">{{ $group['label'] }}</span>
                        @unless ($protected)
                            <input type="checkbox" class="form-check-input group-toggle" data-group="{{ $group['key'] }}" title="Toggle all in {{ $group['label'] }}">
                        @endunless
                    </div>
                    @foreach ($group['permissions'] as $perm)
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $perm['name'] }}"
                                   id="perm-{{ $perm['name'] }}"
                                   class="form-check-input perm-check group-{{ $group['key'] }}"
                                   @checked($protected || in_array($perm['name'], $checked))
                                   @disabled($protected)>
                            <label class="form-check-label small text-capitalize" for="perm-{{ $perm['name'] }}">
                                {{ str_replace('_', ' ', $perm['action']) }}
                                @if ($perm['action'] === 'view')<i class="bi bi-eye text-muted ms-1" title="Controls menu visibility"></i>@endif
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save role</button>
</div>

@unless ($protected)
@push('scripts')
<script>
    (function () {
        // Per-group toggle: ticks/unticks every permission in that group.
        document.querySelectorAll('.group-toggle').forEach(function (toggle) {
            var boxes = document.querySelectorAll('.group-' + toggle.dataset.group);
            var sync = function () {
                toggle.checked = boxes.length && Array.prototype.every.call(boxes, function (b) { return b.checked; });
            };
            toggle.addEventListener('change', function () {
                boxes.forEach(function (b) { b.checked = toggle.checked; });
            });
            boxes.forEach(function (b) { b.addEventListener('change', sync); });
            sync();
        });

        // Global select-all toggle.
        var all = document.getElementById('toggle-all');
        all && all.addEventListener('click', function () {
            var boxes = document.querySelectorAll('.perm-check');
            var turnOn = !Array.prototype.every.call(boxes, function (b) { return b.checked; });
            boxes.forEach(function (b) { b.checked = turnOn; });
            document.querySelectorAll('.group-toggle').forEach(function (t) { t.checked = turnOn; });
            this.innerHTML = turnOn
                ? '<i class="bi bi-square me-1"></i> Clear all'
                : '<i class="bi bi-check2-square me-1"></i> Select all';
        });
    })();
</script>
@endpush
@endunless
