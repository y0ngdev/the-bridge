<?php

use App\Models\Alumnus;
use App\Models\Department;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Todo: Can you clean up this file to cater for just new entries
    public function up(): void
    {
        // Step 1: Add nullable department_id column
        Schema::table('alumni', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('phones')->constrained()->nullOnDelete();
        });

        // Step 2: Migrate existing department strings to relationships
        $alumni = Alumnus::whereNotNull('department')->where('department', '!=', '')->get();

        foreach ($alumni as $alumnus) {
            $departmentName = trim($alumnus->department);

            if (empty($departmentName)) {
                continue;
            }

            // Try to find existing department by name or code
            $department = Department::where('name', $departmentName)
                ->orWhere('code', $departmentName)
                ->first();

            // If not found, create new department
            if (! $department) {
                // Generate a code from the name (first letters of each word)
                $words = explode(' ', $departmentName);
                $code = '';
                foreach ($words as $word) {
                    $code .= strtoupper(substr($word, 0, 1));
                }

                // Ensure code is unique
                $baseCode = $code;
                $counter = 1;
                while (Department::where('code', $code)->exists()) {
                    $code = $baseCode.$counter;
                    $counter++;
                }

                $department = Department::create([
                    'code' => $code,
                    'name' => $departmentName,
                    'school' => 'Unknown', // Will need manual update
                ]);
            }

            // Update the alumnus with the department_id
            $alumnus->update(['department_id' => $department->id]);
        }

        // Step 3: Drop the old department string column
        Schema::table('alumni', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add back the department string column
        Schema::table('alumni', function (Blueprint $table) {
            $table->string('department')->nullable()->after('phones');
        });

        // Step 2: Migrate department names back to string
        $alumni = Alumnus::with('department')->whereNotNull('department_id')->get();

        foreach ($alumni as $alumnus) {
            if ($alumnus->department) {
                $alumnus->update(['department' => $alumnus->department->name]);
            }
        }

        // Step 3: Drop the department_id column
        Schema::table('alumni', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
