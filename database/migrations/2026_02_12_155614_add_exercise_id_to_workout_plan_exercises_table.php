<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_plan_exercises', function (Blueprint $table) {
            $table->unsignedBigInteger('exercise_id')->nullable()->after('workout_plan_id');
            $table->string('execution_style')->nullable()->after('rest_seconds');
            $table->string('rpe')->nullable()->after('tempo');
        });
    }

    public function down(): void
    {
        Schema::table('workout_plan_exercises', function (Blueprint $table) {
            $table->dropColumn(['exercise_id', 'execution_style', 'rpe']);
        });
    }
};
