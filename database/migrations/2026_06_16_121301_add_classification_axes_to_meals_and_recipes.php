<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('format')->nullable()->after('cuisine');
            $table->string('hero_veg')->nullable()->after('format');
            $table->string('source_locale', 5)->nullable()->after('hero_veg');
        });

        Schema::table('meals', function (Blueprint $table) {
            $table->string('format')->nullable()->after('cuisine');
            $table->string('hero_veg')->nullable()->after('format');
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn(['format', 'hero_veg']);
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['format', 'hero_veg', 'source_locale']);
        });
    }
};
