<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calorie_trackings', function (Blueprint $table) {
            $table->index(['user_id', 'meal_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('calorie_trackings', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'meal_type', 'created_at']);
        });
    }
};
