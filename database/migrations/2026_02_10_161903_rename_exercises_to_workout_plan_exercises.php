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
        Schema::rename('exercises', 'workout_plan_exercises');

        Schema::table('workout_tracking_exercises', function (Blueprint $table) {
            $table->dropForeign(['exercise_id']);
            $table->renameColumn('exercise_id', 'workout_plan_exercise_id');
            $table->foreign('workout_plan_exercise_id')
                ->references('id')
                ->on('workout_plan_exercises')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_tracking_exercises', function (Blueprint $table) {
            $table->dropForeign(['workout_plan_exercise_id']);
            $table->renameColumn('workout_plan_exercise_id', 'exercise_id');
            $table->foreign('exercise_id')
                ->references('id')
                ->on('exercises')
                ->onDelete('cascade');
        });

        Schema::rename('workout_plan_exercises', 'exercises');
    }
};
