@php $enrolment = $enrolment ?? null; @endphp
<div class="content-card">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Student *</label>
            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                <option value="">— Select —</option>
                @foreach ($students as $s)
                    <option value="{{ $s->id }}" @selected((int) old('student_id', $enrolment?->student_id) === $s->id)>
                        {{ $s->student_number }} · {{ $s->full_name }}
                    </option>
                @endforeach
            </select>
            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Course *</label>
            <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                <option value="">— Select —</option>
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}" @selected((int) old('course_id', $enrolment?->course_id) === $c->id)>
                        [{{ $c->code }}] {{ $c->title }}
                    </option>
                @endforeach
            </select>
            @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Schedule</label>
            <select name="schedule_id" class="form-select">
                <option value="">No specific schedule</option>
                @forelse ($schedules as $s)
                    <option value="{{ $s->id }}" data-course="{{ $s->course_id }}"
                            @selected((int) old('schedule_id', $enrolment?->schedule_id) === $s->id)>
                        [{{ $s->course?->code }}] {{ $s->course?->title }} ·
                        {{ $s->start_date->format('d M Y') }}@if ($s->end_date && !$s->end_date->isSameDay($s->start_date))–{{ $s->end_date->format('d M') }}@endif
                        @if ($s->location) · {{ $s->location->name }} @endif
                        ({{ $s->status }})
                    </option>
                @empty
                    <option value="" disabled>No schedules available yet</option>
                @endforelse
            </select>
            <div class="form-text">Optional — pick a specific cohort/run, or leave blank for an open enrolment.</div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Enrolled on</label>
            <input type="date" name="enrolled_on" value="{{ old('enrolled_on', optional($enrolment?->enrolled_on)->format('Y-m-d') ?? now()->toDateString()) }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach (['enrolled','in_progress','completed','failed','withdrawn'] as $st)
                    <option value="{{ $st }}" @selected(old('status', $enrolment?->status ?? 'enrolled') === $st)>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        @if ($enrolment)
            <div class="col-md-3">
                <label class="form-label">Completed on</label>
                <input type="date" name="completed_on" value="{{ old('completed_on', optional($enrolment->completed_on)->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Final score (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="final_score" value="{{ old('final_score', $enrolment->final_score) }}" class="form-control">
            </div>
        @endif
        <div class="col-12">
            <label class="form-label">Trainer comments</label>
            <textarea name="trainer_comments" rows="3" class="form-control">{{ old('trainer_comments', $enrolment?->trainer_comments) }}</textarea>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('enrolments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Save</button>
</div>
