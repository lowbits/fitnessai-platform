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
        Schema::create('workout_tracking_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_tracking_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['workout_tracking_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_tracking_exercises');
    }
};

