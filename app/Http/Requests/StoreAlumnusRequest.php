<?php

namespace App\Http\Requests;

use App\Enums\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAlumnusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phones' => 'nullable|array',
            'department' => ['nullable', new Enum(Department::class)],
            'gender' => 'nullable|string|in:M,F',
            'birth_date' => 'nullable|string|max:50',
            'tenure_id' => 'required|exists:tenures,id',
            'unit' => 'nullable|string',
            'state' => 'nullable|string',
            'address' => 'nullable|string',
            'past_exco_office' => 'nullable|string|max:255',
            'current_exco_office' => 'nullable|string|max:255',
            'is_futa_staff' => 'nullable|boolean',
        ];
    }
}
