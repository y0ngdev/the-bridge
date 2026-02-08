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
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // REMOVE UNIQUE EMAILS
            $table->string('email')->nullable()->unique();
            $table->json('phones')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('gender', ['M', 'F'])->nullable();

            $table->boolean('is_futa_staff')->default(false);
            $table->date('birth_date')->nullable();

            $table->string('past_exco_office')->nullable();
            $table->string('current_exco_office')->nullable();

            $table->string('unit')->nullable();
            $table->string('state')->nullable();
            $table->text('address')->nullable();

            $table->string('marital_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('current_employer')->nullable();

            $table->foreignId('tenure_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
