<?php

namespace Database\Seeders;

use App\Models\Alumnus;
use App\Models\Department;
use App\Models\Tenure;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

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
 $this->seedIfEmpty(Alumnus::class, AlumnusSeeder::class);

      
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
