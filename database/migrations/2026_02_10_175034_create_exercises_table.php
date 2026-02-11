<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name');                                     // English canonical name: "Cable Fly"
            $table->string('slug')->unique();                           // URL-safe: "cable-fly"

            // Classification
            $table->string('type');                                     // strength, cardio, warmup, cooldown, stretch
            $table->string('movement_pattern')->nullable();             // squat, hinge, push, pull, carry, rotation, lunge, press
            $table->string('mechanic')->nullable();                     // compound, isolation
            $table->string('force_type')->nullable();                   // push, pull, static
            $table->string('laterality')->nullable();                   // bilateral, unilateral
            $table->string('body_region')->nullable();                  // upper, lower, full_body, core

            // Muscles
            $table->json('primary_muscles')->nullable();                // ["chest", "triceps"]
            $table->json('secondary_muscles')->nullable();              // ["front_delts"]

            // Equipment & Logistics
            $table->json('equipment')->nullable();                      // ["barbell", "squat_rack"]
            $table->json('training_places')->nullable();                // ["gym", "home", "outdoor", "anywhere"]
            $table->string('difficulty')->nullable();                   // beginner, intermediate, advanced

            // Content (English default)
            $table->text('description')->nullable();
            $table->json('instructions')->nullable();
            $table->text('form_cues')->nullable();
            $table->string('video_url')->nullable();
            $table->string('image')->nullable();

            // Categorization
            $table->json('event_tags')->nullable();                     // ["hyrox", "crossfit", "marathon"]


            // Admin
            $table->boolean('is_verified')->default(false);
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('type');
            $table->index('movement_pattern');
            $table->index('mechanic');
            $table->index('body_region');
            $table->index('difficulty');
            $table->index('is_verified');
        });

        Schema::create('exercise_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);                                // "de", "en", "es"

            // Localized content
            $table->string('name');                                     // "Kabel-Fliegende"
            $table->json('aliases')->nullable();                        // ["Kabel Brustfliegende", "Kabelzug Fliegende"]
            $table->text('description')->nullable();
            $table->json('instructions')->nullable();
            $table->text('form_cues')->nullable();

            $table->timestamps();

            $table->unique(['exercise_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_translations');
        Schema::dropIfExists('exercises');
    }
};
