<?php

namespace Database\Seeders\AlumniSeeder;

use App\Models\Alumnus;
use App\Models\Tenure;
use Illuminate\Database\Seeder;

class Alumni2000_2001Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenure = Tenure::updateOrCreate(
            ['year' => '2000-2001'],
            ['name' => '2000-2001 Set']
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

        $this->command->info("Seeded {$tenure->year} alumni: ".count($alumni).' records');
    }
}
