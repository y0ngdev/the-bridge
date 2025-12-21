<?php

namespace Database\Seeders;

use App\Models\Tenure;
use Illuminate\Database\Seeder;

class TenureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startYear = 1987;
        $endYear = 2025;

        for ($year = $startYear; $year <= $endYear; $year++) {
            Tenure::create([
                'name' => '',
                'year' => $year.'-'.($year + 1),
            ]);
        }
    }
}
