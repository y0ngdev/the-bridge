<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenures', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('year');
            $table->date('start_date')->nullable()->after('is_active');
            $table->date('end_date')->nullable()->after('start_date');
        });

        Schema::table('communication_logs', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->after('user_id')->constrained('tenures')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communication_logs', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
        });

        Schema::table('tenures', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'start_date', 'end_date']);
        });
    }
};
