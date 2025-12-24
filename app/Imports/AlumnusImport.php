<?php

namespace App\Imports;

use App\Models\Alumnus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlumnusImport implements ToModel, WithHeadingRow
{
    /**
     * Create a new AlumnusImport instance.
     */
    public function __construct(
        protected int $tenureId
    ) {}

    /**
     * Create a model from an Excel row.
     */
    public function model(array $row): ?Alumnus
    {
        return new Alumnus([
            'name' => $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'phones' => $this->parsePhones($row['phones'] ?? $row['phone'] ?? null),
            'gender' => $row['gender'] ?? null,
            'birth_date' => $this->parseBirthDate($row['birth_date'] ?? $row['birthday'] ?? null),
            'tenure_id' => $this->tenureId,
            'state' => $row['state'] ?? null,
            'unit' => $row['unit'] ?? null,
            'address' => $row['address'] ?? $row['home_address'] ?? null,
        ]);
    }

    /**
     * Parse phone numbers from comma-separated string.
     */
    protected function parsePhones(?string $phones): ?array
    {
        if (! $phones) {
            return null;
        }

        return array_map('trim', preg_split('/[,;]/', $phones));
    }

    /**
     * Parse birth date from various formats.
     */
    protected function parseBirthDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            $value = trim($value);

            // Handle "Day-Month" or "Day/Month" formats (e.g., "20-12", "20/12")
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})$/', $value, $matches)) {
                return sprintf('2000-%02d-%02d', $matches[2], $matches[1]);
            }

            // Handle "Day Month" formats (e.g., "20 Dec", "20 December")
            if (preg_match('/^(\d{1,2})\s+([a-zA-Z]+)$/', $value, $matches)) {
                $date = \Carbon\Carbon::parse("{$matches[1]} {$matches[2]} 2000");

                return $date->format('Y-m-d');
            }

            // Try standard parsing
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
