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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_name');
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->default(28);

            $table->integer('daily_calories');
            $table->integer('daily_protein_g');
            $table->integer('daily_carbs_g');
            $table->integer('daily_fat_g');

            $table->integer('workouts_per_week');

            // Metadata
            $table->json('generation_params')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

