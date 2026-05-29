<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('courses.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'code'           => ['nullable', 'string', 'max:30', 'unique:courses,code'],
            'title'          => ['required', 'string', 'max:200'],
            'category_id'    => ['nullable', 'exists:course_categories,id'],
            'description'    => ['nullable', 'string', 'max:5000'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:2000'],
            'passing_score'  => ['required', 'integer', 'min:0', 'max:100'],
            'fee'            => ['nullable', 'numeric', 'min:0'],
            'is_active'      => ['nullable', 'boolean'],
            'modules'                 => ['nullable', 'array'],
            'modules.*.title'         => ['nullable', 'string', 'max:200'],
            'modules.*.description'   => ['nullable', 'string', 'max:2000'],
            'modules.*.duration_hours'=> ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
