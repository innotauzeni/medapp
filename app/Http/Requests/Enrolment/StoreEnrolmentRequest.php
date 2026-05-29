<?php

namespace App\Http\Requests\Enrolment;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrolmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('enrolments.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'student_id'  => ['required', 'exists:students,id'],
            'course_id'   => ['required', 'exists:courses,id'],
            'schedule_id' => ['nullable', 'exists:course_schedules,id'],
            'status'      => ['nullable', 'in:enrolled,in_progress,completed,failed,withdrawn'],
            'enrolled_on' => ['nullable', 'date'],
            'trainer_comments' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
