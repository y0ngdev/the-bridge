<?php

namespace App\Exports;

use App\Models\Alumnus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlumnusExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $fields;

    protected array $filters;

    // All available fields with their configuration
    protected array $availableFields = [
        'name' => ['heading' => 'Name', 'getter' => 'name'],
        'email' => ['heading' => 'Email', 'getter' => 'email'],
        'phones' => ['heading' => 'Phone(s)', 'getter' => 'phones'],
        'department' => ['heading' => 'Department', 'getter' => 'department'],
        'gender' => ['heading' => 'Gender', 'getter' => 'gender'],
        'birth_date' => ['heading' => 'Birthday', 'getter' => 'birth_date'],
        'tenure' => ['heading' => 'Tenure', 'getter' => 'tenure'],
        'unit' => ['heading' => 'Unit', 'getter' => 'unit'],
        'state' => ['heading' => 'State', 'getter' => 'state'],
        'address' => ['heading' => 'Address', 'getter' => 'address'],
        'past_exco_office' => ['heading' => 'Past Exco Office (School)', 'getter' => 'past_exco_office'],
        'current_exco_office' => ['heading' => 'Current Exco Office (Alumni)', 'getter' => 'current_exco_office'],
        'is_futa_staff' => ['heading' => 'Is FUTA Staff', 'getter' => 'is_futa_staff'],
    ];

    public function __construct(array $fields = [], array $filters = [])
    {
        // If no fields specified, use all fields
        $this->fields = empty($fields) ? array_keys($this->availableFields) : $fields;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Alumnus::with('tenure');

        // Apply filters
        if (!empty($this->filters['tenure_id'])) {
            $query->where('tenure_id', $this->filters['tenure_id']);
        }

        if (!empty($this->filters['unit'])) {
            $query->where('unit', $this->filters['unit']);
        }

        if (!empty($this->filters['state'])) {
            $query->where('state', $this->filters['state']);
        }

        if (!empty($this->filters['gender'])) {
            $query->whereRaw('LOWER(gender) = ?', [strtolower($this->filters['gender'])]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->fields as $field) {
            if (isset($this->availableFields[$field])) {
                $headings[] = $this->availableFields[$field]['heading'];
            }
        }

        return $headings;
    }

    public function map($alumnus): array
    {
        $row = [];
        foreach ($this->fields as $field) {
            if (!isset($this->availableFields[$field])) {
                continue;
            }

            $row[] = match ($field) {
                'phones' => is_array($alumnus->phones) ? implode(', ', $alumnus->phones) : $alumnus->phones,
                'birth_date' => $alumnus->birth_date?->format('d M'),
                'tenure' => $alumnus->tenure?->year,
                'unit' => $alumnus->unit instanceof \App\Enums\Unit ? $alumnus->unit->value : $alumnus->unit,
                'department' => $alumnus->department,
                'state' => $alumnus->state instanceof \App\Enums\NigerianState ? $alumnus->state->value : $alumnus->state,
                'is_futa_staff' => $alumnus->is_futa_staff ? 'Yes' : 'No',
                default => $alumnus->{$field},
            };
        }

        return $row;
    }

    public static function getAvailableFieldKeys(): array
    {
        return array_keys((new self)->availableFields);
    }

    public static function getAvailableFields(): array
    {
        $instance = new self;
        $fields = [];
        foreach ($instance->availableFields as $key => $config) {
            $fields[] = ['key' => $key, 'label' => $config['heading']];
        }

        return $fields;
    }
}
