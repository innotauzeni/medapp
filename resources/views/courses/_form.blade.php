@php $course = $course ?? null; @endphp
<div class="content-card">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Code</label>
            <input name="code" value="{{ old('code', $course?->code) }}" class="form-control @error('code') is-invalid @enderror" placeholder="Auto-generated if empty">
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Title *</label>
            <input name="title" value="{{ old('title', $course?->title) }}" class="form-control @error('title') is-invalid @enderror" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">—</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((int) old('category_id', $course?->category_id) === $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $course?->description) }}</textarea>
        </div>
        <div class="col-md-3">
            <label class="form-label">Duration (hours) *</label>
            <input type="number" min="1" name="duration_hours" value="{{ old('duration_hours', $course?->duration_hours ?? 8) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Passing score (%) *</label>
            <input type="number" min="0" max="100" name="passing_score" value="{{ old('passing_score', $course?->passing_score ?? 50) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Fee (R)</label>
            <input type="number" min="0" step="0.01" name="fee" value="{{ old('fee', $course?->fee ?? 0) }}" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
                       @checked(old('is_active', $course?->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>
</div>

<div class="content-card mt-3">
    <h2 class="h6 mb-3">Modules</h2>
    <div id="modules-list">
        @php $mods = old('modules', $course?->modules?->toArray() ?? []); @endphp
        @forelse ($mods as $i => $m)
            <div class="row g-2 mb-2 module-row">
                <div class="col-md-4"><input name="modules[{{ $i }}][title]" value="{{ $m['title'] ?? '' }}" class="form-control" placeholder="Module title"></div>
                <div class="col-md-6"><input name="modules[{{ $i }}][description]" value="{{ $m['description'] ?? '' }}" class="form-control" placeholder="Description"></div>
                <div class="col-md-1"><input type="number" min="1" name="modules[{{ $i }}][duration_hours]" value="{{ $m['duration_hours'] ?? 1 }}" class="form-control" placeholder="hrs"></div>
                <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-module"><i class="bi bi-trash"></i></button></div>
            </div>
        @empty
        @endforelse
    </div>
    <button type="button" id="add-module" class="btn btn-outline-secondary btn-sm mt-2"><i class="bi bi-plus"></i> Add module</button>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>

@push('scripts')
<script>
    (function () {
        const list = document.getElementById('modules-list');
        let idx = list.querySelectorAll('.module-row').length;
        document.getElementById('add-module').addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 module-row';
            row.innerHTML = `
                <div class="col-md-4"><input name="modules[${idx}][title]" class="form-control" placeholder="Module title"></div>
                <div class="col-md-6"><input name="modules[${idx}][description]" class="form-control" placeholder="Description"></div>
                <div class="col-md-1"><input type="number" min="1" name="modules[${idx}][duration_hours]" value="1" class="form-control"></div>
                <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-module"><i class="bi bi-trash"></i></button></div>`;
            list.appendChild(row);
            idx++;
        });
        list.addEventListener('click', e => {
            if (e.target.closest('.remove-module')) {
                e.target.closest('.module-row').remove();
            }
        });
    })();
</script>
@endpush
