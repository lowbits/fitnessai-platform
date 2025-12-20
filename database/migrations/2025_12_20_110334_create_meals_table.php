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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_plan_id')->constrained('meal_plans')->onDelete('cascade');
            $table->enum('type', ['breakfast', 'lunch', 'snack', 'dinner']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('calories');
            $table->integer('protein_g');
            $table->integer('carbs_g');
            $table->integer('fat_g');
            $table->integer('fiber_g')->nullable();
            $table->integer('sugar_g')->nullable();
            $table->json('ingredients')->nullable();
            $table->json('instructions')->nullable();
            $table->integer('prep_time_minutes')->nullable();
            $table->integer('cook_time_minutes')->nullable();
            $table->string('difficulty')->nullable();
            $table->integer('servings')->default(1);
            $table->json('tags')->nullable();
            $table->json('allergens')->nullable();
            $table->timestamps();

            $table->index(['meal_plan_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
