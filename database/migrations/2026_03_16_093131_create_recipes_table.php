<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('ingredients')->nullable();
            $table->json('instructions')->nullable();
            $table->integer('prep_time_minutes')->nullable();
            $table->integer('cook_time_minutes')->nullable();
            $table->string('difficulty')->nullable();
            $table->integer('servings')->default(1);
            $table->integer('calories')->nullable();
            $table->decimal('protein_g', 8, 2)->nullable();
            $table->decimal('carbs_g', 8, 2)->nullable();
            $table->decimal('fat_g', 8, 2)->nullable();
            $table->decimal('fiber_g', 8, 2)->nullable();
            $table->decimal('sugar_g', 8, 2)->nullable();
            $table->json('tags')->nullable();
            $table->json('allergens')->nullable();
            $table->string('primary_protein')->nullable();
            $table->string('cuisine')->nullable();
            $table->string('image')->nullable();
            $table->string('image_full')->nullable();
            $table->string('image_isolated')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('needs_translation')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('difficulty');
            $table->index('primary_protein');
            $table->index('cuisine');
            $table->index('is_verified');
        });

        Schema::create('recipe_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug')->nullable();
            $table->json('aliases')->nullable();
            $table->text('description')->nullable();
            $table->json('instructions')->nullable();
            $table->timestamps();

            $table->unique(['recipe_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });

        Schema::table('meals', function (Blueprint $table) {
            $table->foreignId('recipe_id')->nullable()->after('meal_plan_id')->constrained('recipes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recipe_id');
        });

        Schema::dropIfExists('recipe_translations');
        Schema::dropIfExists('recipes');
    }
};
