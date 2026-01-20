<?php

namespace App\Http\Requests\Concerns;

trait AlumnusRules
{
    /**
     * Get the shared validation rules for an alumnus.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function alumnusRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phones' => 'nullable|array',
            'department' => 'nullable|string|exists:departments,code',
            'gender' => 'nullable|string|in:M,F',
            'birth_date' => 'nullable|string|max:50',
            'tenure_id' => 'required|exists:tenures,id',
            'unit' => 'nullable|string',
            'state' => 'nullable|string',
            'address' => 'nullable|string',
            'past_exco_office' => 'nullable|string|max:255',
            'current_exco_office' => 'nullable|string|max:255',
            'is_futa_staff' => 'nullable|boolean',
            'is_overseas' => 'nullable|boolean',
        ];
    }
}
