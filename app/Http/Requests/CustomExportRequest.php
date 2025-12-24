<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fields' => ['nullable', 'array'],
            'fields.*' => ['string', 'in:name,email,phones,state,unit,department,gender,birth_date,tenure,current_exco_office,past_exco_offices,is_futa_staff,address'],
            'state' => ['nullable', 'string'],
            'unit' => ['nullable', 'string'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
