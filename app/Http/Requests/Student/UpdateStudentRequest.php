<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('students.update') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('student');
        if (is_object($id)) { $id = $id->id; }

        return [
            'student_number' => ['nullable', 'string', 'max:50', Rule::unique('students', 'student_number')->ignore($id)],
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'email'          => ['nullable', 'email', 'max:150'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'date_of_birth'  => ['nullable', 'date'],
            'gender'         => ['nullable', 'in:male,female,other'],
            'id_type'        => ['required', 'in:id,passport'],
            'id_number'      => ['nullable', 'string', 'max:50'],
            'nationality'    => ['nullable', 'string', 'max:80'],
            'address_line1'  => ['nullable', 'string', 'max:200'],
            'city'           => ['nullable', 'string', 'max:100'],
            'province'       => ['nullable', 'string', 'max:100'],
            'country'        => ['nullable', 'string', 'max:80'],
            'emergency_contact_name'         => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone'        => ['nullable', 'string', 'max:30'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:60'],
            'employer'                       => ['nullable', 'string', 'max:120'],
            'job_title'                      => ['nullable', 'string', 'max:120'],
            'notes'                          => ['nullable', 'string', 'max:2000'],
            'profile_photo'                  => ['nullable', 'image', 'max:4096'],
            'status'                         => ['nullable', 'in:active,archived'],
        ];
    }
}
