<?php

namespace Database\Seeders\AlumniSeeder;

use App\Models\Alumnus;
use App\Models\Tenure;
use Illuminate\Database\Seeder;

class Alumni2002_2003Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenure = Tenure::firstOrCreate(
            ['year' => '2002-2003'],
             ['name' => 'In the Portters Hand Generation',
           ]

        );

        $alumni = [

        ];

        foreach ($alumni as $data) {
            if (empty($data['name'])) {
                continue;
            }

            Alumnus::updateOrCreate(
                ['name' => $data['name'], 'tenure_id' => $tenure->id],
                array_merge($data, ['tenure_id' => $tenure->id])
            );
        }

        $this->command->info("Seeded {$tenure->year} alumni: " . count($alumni) . " records");
    }
}
