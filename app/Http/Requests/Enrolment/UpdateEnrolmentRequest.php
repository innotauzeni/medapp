<?php

namespace App\Http\Requests\Enrolment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrolmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('enrolments.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'student_id'       => ['required', 'exists:students,id'],
            'course_id'        => ['required', 'exists:courses,id'],
            'schedule_id'      => ['nullable', 'exists:course_schedules,id'],
            'status'           => ['required', 'in:enrolled,in_progress,completed,failed,withdrawn'],
            'enrolled_on'      => ['nullable', 'date'],
            'completed_on'     => ['nullable', 'date'],
            'final_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'trainer_comments' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
