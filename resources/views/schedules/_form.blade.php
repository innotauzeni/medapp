@php $schedule = $schedule ?? null; @endphp
<div class="card-er card-er-pad">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Course *</label>
            <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                <option value="">— Select —</option>
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}" @selected((int) old('course_id', $schedule?->course_id) === $c->id)>[{{ $c->code }}] {{ $c->title }}</option>
                @endforeach
            </select>
            @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Start date *</label>
            <input type="date" name="start_date" value="{{ old('start_date', optional($schedule?->start_date)->format('Y-m-d')) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">End date *</label>
            <input type="date" name="end_date" value="{{ old('end_date', optional($schedule?->end_date)->format('Y-m-d')) }}" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Location
                <a href="{{ route('locations.create') }}" target="_blank" class="ms-2 small text-decoration-none" title="Open in new tab to create a location">
                    <i class="bi bi-plus-circle"></i> New location
                </a>
            </label>
            <select name="location_id" class="form-select">
                <option value="">— None / TBC —</option>
                @foreach ($locations as $l)
                    <option value="{{ $l->id }}" @selected((int) old('location_id', $schedule?->location_id) === $l->id)>{{ $l->name }}{{ $l->city ? ' · ' . $l->city : '' }}</option>
                @endforeach
            </select>
            @if ($locations->isEmpty())
                <div class="form-text text-warning"><i class="bi bi-exclamation-triangle me-1"></i> No active locations yet. <a href="{{ route('locations.create') }}" target="_blank">Create one first.</a></div>
            @endif
        </div>
        <div class="col-md-6">
            <label class="form-label">Lead trainer</label>
            <select name="lead_trainer_id" class="form-select">
                <option value="">—</option>
                @foreach ($trainers as $t)
                    <option value="{{ $t->id }}" @selected((int) old('lead_trainer_id', $schedule?->lead_trainer_id) === $t->id)>{{ $t->first_name }} {{ $t->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Capacity *</label>
            <input type="number" name="capacity" value="{{ old('capacity', $schedule?->capacity ?? 20) }}" min="1" max="1000" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach (['scheduled','in_progress','completed','cancelled'] as $st)
                    <option value="{{ $st }}" @selected(old('status', $schedule?->status ?? 'scheduled') === $st)>{{ str_replace('_',' ',$st) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $schedule?->notes) }}</textarea>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('schedules.index') }}" class="btn btn-soft">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>
