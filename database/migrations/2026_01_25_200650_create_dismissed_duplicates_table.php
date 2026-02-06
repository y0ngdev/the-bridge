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
        Schema::create('dismissed_duplicates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumnus_id_1')->constrained('alumni')->onDelete('cascade');
            $table->foreignId('alumnus_id_2')->constrained('alumni')->onDelete('cascade');
            $table->foreignId('dismissed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['alumnus_id_1', 'alumnus_id_2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dismissed_duplicates');
    }
};
