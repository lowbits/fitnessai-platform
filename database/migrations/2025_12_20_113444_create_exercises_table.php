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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_plan_id')->constrained('workout_plans')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('name');
            $table->enum('type', ['strength', 'cardio', 'warmup', 'cooldown', 'stretch']);
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('image')->nullable();
            $table->integer('sets')->nullable();
            $table->integer('reps')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('rest_seconds')->nullable();
            $table->string('tempo')->nullable()->comment('e.g., "3-0-1-0" for eccentric-pause-concentric-pause');
            $table->string('weight_recommendation')->nullable()->comment('e.g., "60-70% 1RM" or "Bodyweight"');
            $table->json('muscle_groups')->nullable();
            $table->json('equipment')->nullable();
            $table->text('form_cues')->nullable();
            $table->json('alternatives')->nullable();
            $table->string('difficulty')->nullable();
            $table->timestamps();

            $table->index(['workout_plan_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
