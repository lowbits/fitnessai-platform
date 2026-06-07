<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->json('selected_meals')->nullable();
            $table->json('food_dislikes')->nullable();
            $table->string('cooking_preference')->default('normal');
            $table->string('meal_variety')->default('medium');
            $table->boolean('meal_prep_enabled')->default(false);
            $table->text('favorite_meals')->nullable();
            $table->json('physical_limitations')->nullable();
            $table->text('physical_limitations_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'selected_meals',
                'food_dislikes',
                'cooking_preference',
                'meal_variety',
                'meal_prep_enabled',
                'favorite_meals',
                'physical_limitations',
                'physical_limitations_note',
            ]);
        });
    }
};
