<?php

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietType;
use App\Enums\Gender;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age');
            $table->enum('gender', array_column(Gender::cases(), 'value'));

            $table->decimal('weight_kg', 4, 1)->nullable()->comment('Weight in kg');
            $table->integer('height_cm')->nullable()->comment('Height in cm');

            $table->enum('body_goal', array_column(BodyGoal::cases(), 'value'))->nullable();
            $table->enum('skill_level', array_column(SkillLevel::cases(), 'value'))->nullable();
            $table->enum('activity_level', array_column(ActivityLevel::cases(), 'value'))->nullable();
            $table->enum('training_place', array_column(TrainingPlace::cases(), 'value'))->nullable();
            $table->enum('diet_type', array_column(DietType::cases(), 'value'))->nullable();
            $table->integer('training_sessions_per_week')->default(3);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
