<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Main metrics
            $table->decimal('weight_kg', 5, 2)->comment('Weight in kg');
            $table->decimal('body_fat_percentage', 5, 2)->nullable()->comment('Body fat percentage');
            $table->decimal('muscle_mass_kg', 5, 2)->nullable()->comment('Muscle mass in kg');

            // Circumference measurements (in cm)
            $table->decimal('waist_circumference_cm', 5, 2)->nullable()->comment('Waist circumference in cm');
            $table->decimal('chest_circumference_cm', 5, 2)->nullable()->comment('Chest circumference in cm');
            $table->decimal('hip_circumference_cm', 5, 2)->nullable()->comment('Hip circumference in cm');
            $table->decimal('arm_circumference_cm', 5, 2)->nullable()->comment('Arm circumference in cm');
            $table->decimal('thigh_circumference_cm', 5, 2)->nullable()->comment('Thigh circumference in cm');

            // Additional info
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');

            $table->timestamps();

            $table->index(['user_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_progress');
    }
};

