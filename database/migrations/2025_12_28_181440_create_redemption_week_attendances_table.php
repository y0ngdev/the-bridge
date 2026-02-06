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
        Schema::create('redemption_week_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('redemption_week_sessions')->cascadeOnDelete();
            $table->foreignId('alumnus_id')->constrained('alumni')->cascadeOnDelete();

            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_day');
            $table->timestamps();

            $table->unique(['session_id', 'alumnus_id', 'event_day'], 'unique_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redemption_week_attendances');
    }
};
