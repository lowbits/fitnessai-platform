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
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->date('date');
            $table->integer('day_number');
            $table->enum('status', ['pending', 'generated', 'failed'])->default('pending');
            $table->integer('total_calories')->nullable();
            $table->integer('total_protein_g')->nullable();
            $table->integer('total_carbs_g')->nullable();
            $table->integer('total_fat_g')->nullable();
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
        Schema::dropIfExists('meal_plans');
    }
};

