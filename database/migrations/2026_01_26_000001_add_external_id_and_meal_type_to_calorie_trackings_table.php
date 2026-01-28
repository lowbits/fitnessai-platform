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
        Schema::table('calorie_trackings', function (Blueprint $table) {
            $table->string('external_id', 255)->nullable()->after('meal_id');
            $table->string('meal_type', 32)->nullable()->after('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calorie_trackings', function (Blueprint $table) {
            $table->dropColumn('external_id');
            $table->dropColumn('meal_type');
        });
    }
};
