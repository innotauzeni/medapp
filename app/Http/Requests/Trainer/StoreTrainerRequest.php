<?php

namespace App\Http\Requests\Trainer;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('trainers.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'email'          => ['nullable', 'email', 'max:150', 'unique:trainers,email'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'qualifications' => ['nullable', 'string', 'max:200'],
            'specialty'      => ['nullable', 'string', 'max:120'],
            'bio'            => ['nullable', 'string', 'max:2000'],
            'is_active'      => ['nullable', 'boolean'],
            'user_id'        => ['nullable', 'exists:users,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active', true)]);
    }
}
