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
            // Drop old unique constraint
            $table->dropUnique(['plan_id', 'day_number']);

            // Add a regular (non-unique) index for query performance
            $table->index(['plan_id', 'day_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_plans', function (Blueprint $table) {
            // Drop the regular index
            $table->dropIndex(['plan_id', 'day_number']);

            // Restore original unique constraint
            $table->unique(['plan_id', 'day_number']);
        });
    }
};
