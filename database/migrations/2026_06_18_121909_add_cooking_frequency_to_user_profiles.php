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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('cooking_frequency')->nullable()->after('cooking_preference');
            $table->json('disliked_recipe_ids')->nullable()->after('food_dislikes');
            $table->dropColumn('meal_prep_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['cooking_frequency', 'disliked_recipe_ids']);
            $table->boolean('meal_prep_enabled')->default(false);
        });
    }
};
