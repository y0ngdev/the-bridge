<?php

namespace Database\Seeders\AlumniSeeder;

use App\Models\Alumnus;
use App\Models\Tenure;
use Illuminate\Database\Seeder;

class Alumni2001_2002Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenure = Tenure::updateOrCreate(
            ['year' => '2001-2002'],
            ['name' => '2001-2002 Set']
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
