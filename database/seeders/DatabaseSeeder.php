<?php

namespace Database\Seeders;

use App\Models\Alumnus;
use App\Models\CommunicationLog;
use App\Models\Department;
use App\Models\Tenure;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->seedIfEmpty(User::class, UserSeeder::class);
        $this->seedIfEmpty(Tenure::class, TenureSeeder::class);
        $this->seedIfEmpty(Department::class, DepartmentSeeder::class);
        $this->seedIfEmpty(Alumnus::class, AlumniSeeder::class);
        $this->seedIfEmpty(CommunicationLog::class, CommunicationLogSeeder::class);

        $this->call([
            TvmAlumniSeeder::class,
            AlumniLocationSeeder::class,
            AlumniFormSeeder::class,
            FutaStaffSeeder::class,
            HeirsOfPromiseSeeder::class,
        ]);
    }

    protected function seedIfEmpty(string $model, string $seeder): void
    {
        if ($model::count() === 0) {
            $this->call($seeder);
        } else {
            echo "$model table not empty — skipped $seeder.\n";
        }
    }
}
