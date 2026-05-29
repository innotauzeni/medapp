@extends('layouts.app')
@section('title', 'Issue certificate')
@section('header', 'Issue certificate')

@section('content')
    <form method="POST" action="{{ route('certificates.store') }}">
        @csrf
        <div class="content-card">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Student *</label>
                    <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="">— Select —</option>
                        @foreach ($students as $s)
                            <option value="{{ $s->id }}" @selected((int) old('student_id') === $s->id)>
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
                            <option value="{{ $c->id }}" @selected((int) old('course_id') === $c->id)>[{{ $c->code }}] {{ $c->title }}</option>
                        @endforeach
                    </select>
                    @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Issued at</label>
                    <input type="date" name="issued_at" value="{{ old('issued_at', now()->toDateString()) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Expires at</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Score (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="score" value="{{ old('score') }}" class="form-control">
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary" type="submit"><i class="bi bi-award me-1"></i> Issue</button>
        </div>
    </form>
@endsection
