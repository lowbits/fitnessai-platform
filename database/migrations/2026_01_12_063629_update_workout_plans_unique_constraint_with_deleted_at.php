<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Update unique constraint to allow multiple workouts with same plan_id/day_number
     * if one is soft deleted. This allows creating a new rest day when skipping a workout.
     *
     * Note: MySQL doesn't support partial indexes like PostgreSQL.
     * Instead, we drop the unique constraint entirely and rely on application-level
     * validation to ensure only one active workout per plan_id/day_number.
     */
    public function up(): void
    {
        Schema::table('workout_plans', function (Blueprint $table) {
            // 1. Drop FK
            $table->dropForeign(['plan_id']);

            // 2. Drop UNIQUE (nicht dropIndex!)
            $table->dropUnique(['plan_id', 'day_number']);

            // 3. Add normalen INDEX
            $table->index(['plan_id', 'day_number']);

            // 4. Re-add FK
            $table->foreign('plan_id')
                ->references('id')
                ->on('plans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_plans', function (Blueprint $table) {
            // 1. Drop FK
            $table->dropForeign(['plan_id']);

            // 2. Drop den normalen INDEX (den wir in up() hinzugefügt haben)
            $table->dropIndex(['plan_id', 'day_number']);

            // 3. Add UNIQUE wieder zurück (wie es vorher war)
            $table->unique(['plan_id', 'day_number']);

            // 4. Re-add FK
            $table->foreign('plan_id')
                ->references('id')
                ->on('plans')
                ->onDelete('cascade');
        });
    }
};
