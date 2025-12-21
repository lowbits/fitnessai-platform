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
        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->date('date');
            $table->integer('day_number');
            $table->enum('status', ['pending', 'generated', 'failed'])->default('pending');
            $table->string('workout_name');
            $table->enum('workout_type', ['strength', 'cardio', 'hiit', 'rest', 'mobility'])->default('strength');
            $table->integer('estimated_duration_minutes')->nullable();
            $table->integer('estimated_calories_burned')->nullable();
            $table->string('difficulty')->nullable();
            $table->text('description')->nullable();
            $table->json('muscle_groups')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'day_number']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_plans');
    }
};

