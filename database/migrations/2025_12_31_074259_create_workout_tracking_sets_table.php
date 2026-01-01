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
        Schema::create('workout_tracking_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_tracking_exercise_id')->constrained('workout_tracking_exercises')->onDelete('cascade');
            $table->integer('set_number');
            $table->integer('reps')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('duration')->nullable();
            $table->integer('rpe')->nullable()->comment('Rate of perceived exertion 1-10');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['workout_tracking_exercise_id', 'set_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_tracking_sets');
    }
};
