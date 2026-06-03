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
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label class="form-label mb-0" for="course-description">Description</label>
                <button type="button" id="ai-describe" class="btn btn-soft btn-sm"
                        data-url="{{ route('courses.ai-generate') }}"
                        title="Generate a description and modules with AI">
                    <i class="bi bi-stars me-1"></i> Write with AI
                </button>
            </div>
            <textarea name="description" id="course-description" rows="6" class="form-control">{{ old('description', $course?->description) }}</textarea>
            <div class="form-text" id="ai-describe-status">Type a title above, then let Gemini draft a ~500&nbsp;word description <strong>and the modules below</strong> — all editable.</div>
        </div>
        <div class="col-12">
            <label class="form-label">Feature image</label>
            @if ($course?->feature_image_url)
                <div class="mb-2 d-flex align-items-center gap-3">
                    <img src="{{ $course->feature_image_url }}" alt="Current feature image"
                         style="height:72px;width:auto;border-radius:.5rem;object-fit:cover;">
                    <div class="form-check">
                        <input type="checkbox" id="remove_image" name="remove_image" value="1" class="form-check-input">
                        <label class="form-check-label small" for="remove_image">Remove current image</label>
                    </div>
                </div>
            @endif
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   class="form-control @error('image') is-invalid @enderror">
            <div class="form-text">JPG, PNG or WebP, up to 4&nbsp;MB.</div>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

        const esc = (s) => String(s ?? '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');

        function addModuleRow(m = {}) {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 module-row';
            row.innerHTML = `
                <div class="col-md-4"><input name="modules[${idx}][title]" class="form-control" placeholder="Module title" value="${esc(m.title)}"></div>
                <div class="col-md-6"><input name="modules[${idx}][description]" class="form-control" placeholder="Description" value="${esc(m.description)}"></div>
                <div class="col-md-1"><input type="number" min="1" name="modules[${idx}][duration_hours]" value="${parseInt(m.duration_hours, 10) || 1}" class="form-control"></div>
                <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-module"><i class="bi bi-trash"></i></button></div>`;
            list.appendChild(row);
            idx++;
        }

        document.getElementById('add-module').addEventListener('click', () => addModuleRow());
        list.addEventListener('click', e => {
            if (e.target.closest('.remove-module')) {
                e.target.closest('.module-row').remove();
            }
        });

        // "Write with AI" — drafts the description AND modules from the title via Gemini.
        const btn = document.getElementById('ai-describe');
        if (!btn) return;
        const textarea = document.getElementById('course-description');
        const status   = document.getElementById('ai-describe-status');
        const token    = document.querySelector('meta[name="csrf-token"]')?.content;

        const setStatus = (msg, kind) => {
            status.innerHTML = msg;
            status.className = 'form-text ' + (kind ? 'text-' + kind : 'text-muted');
        };

        btn.addEventListener('click', async () => {
            const title = (document.querySelector('input[name="title"]')?.value || '').trim();
            if (!title) {
                setStatus('Enter a course title first.', 'danger');
                return;
            }
            const hasContent = textarea.value.trim() || list.querySelectorAll('.module-row').length;
            if (hasContent && !confirm('Replace the current description and modules with AI-generated content?')) {
                return;
            }

            const catSel = document.querySelector('select[name="category_id"]');
            const category = catSel && catSel.value ? catSel.options[catSel.selectedIndex].text : '';

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Writing…';
            setStatus('Asking Gemini to draft a description and modules…', 'muted');

            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ title, category }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Request failed.');

                if (data.description) textarea.value = data.description;

                let added = 0;
                if (Array.isArray(data.modules) && data.modules.length) {
                    list.querySelectorAll('.module-row').forEach(r => r.remove());
                    idx = 0;
                    data.modules.forEach(m => { addModuleRow(m); added++; });
                }
                setStatus(`Draft inserted${added ? ` with ${added} module${added === 1 ? '' : 's'}` : ''} — review and edit before saving.`, 'success');
            } catch (e) {
                setStatus(e.message || 'Could not generate content.', 'danger');
            } finally {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        });
    })();
</script>
@endpush
