<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', Rule::unique('departments', 'code')->ignore($this->route('department'))],
            'name' => ['required', 'string', 'max:255'],
            'school' => ['nullable', 'string', 'max:50'],
        ];
    }
}
