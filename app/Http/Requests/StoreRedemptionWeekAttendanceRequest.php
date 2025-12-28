<?php

namespace App\Http\Requests;

use App\Enums\RedemptionWeekDay;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRedemptionWeekAttendanceRequest extends FormRequest
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
            'alumnus_ids' => ['required', 'array', 'min:1'],
            'alumnus_ids.*' => ['required', 'integer', 'exists:alumni,id'],
            'event_day' => ['required', 'string', Rule::enum(RedemptionWeekDay::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'alumnus_ids.required' => 'At least one alumnus must be selected.',
            'alumnus_ids.min' => 'At least one alumnus must be selected.',
            'event_day.required' => 'Please select the event day.',
        ];
    }
}
