<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE workout_plan_exercises
            SET exercise_id = (
                SELECT e.id FROM exercises e
                WHERE LOWER(workout_plan_exercises.original_name) = LOWER(e.name)
                LIMIT 1
            )
            WHERE original_name IS NOT NULL
              AND exercise_id IS NULL
        ');

        Schema::table('workout_plan_exercises', function (Blueprint $table) {
            $table->foreign('exercise_id')->references('id')->on('exercises');
        });
    }

    public function down(): void
    {
        Schema::table('workout_plan_exercises', function (Blueprint $table) {
            $table->dropForeign(['exercise_id']);
            $table->unsignedBigInteger('exercise_id')->nullable()->change();
        });

        DB::table('workout_plan_exercises')->update(['exercise_id' => null]);
    }
};
