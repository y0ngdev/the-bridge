<?php

namespace App\Imports;

use App\Models\Alumnus;
use App\Models\Department;
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
            'department_id' => $this->findOrCreateDepartment($row['department'] ?? null),
            'gender' => $row['gender'] ?? null,
            'birth_date' => $this->parseBirthDate($row['birth_date'] ?? $row['birthday'] ?? null),
            'tenure_id' => $this->tenureId,
            'state' => $row['state'] ?? null,
            'unit' => $row['unit'] ?? null,
            'address' => $row['address'] ?? $row['home_address'] ?? null,
        ]);
    }

    /**
     * Find or create a department from string.
     */
    protected function findOrCreateDepartment(?string $department): ?int
    {
        if (! $department || empty(trim($department))) {
            return null;
        }

        $departmentName = trim($department);

        // Try to find existing department by name or code
        $dept = Department::where('name', $departmentName)
            ->orWhere('code', $departmentName)
            ->first();

        if ($dept) {
            return $dept->id;
        }

        // Create new department
        $words = explode(' ', $departmentName);
        $code = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }

        // Ensure code is unique
        $baseCode = $code ?: 'DEPT';
        $code = $baseCode;
        $counter = 1;
        while (Department::where('code', $code)->exists()) {
            $code = $baseCode.$counter;
            $counter++;
        }

        $dept = Department::create([
            'code' => $code,
            'name' => $departmentName,
            'school' => 'Unknown',
        ]);

        return $dept->id;
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
