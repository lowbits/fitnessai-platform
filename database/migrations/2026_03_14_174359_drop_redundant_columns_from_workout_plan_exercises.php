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
        Schema::table('workout_plan_exercises', function (Blueprint $table) {
            $table->dropIndex('exercises_original_name_index');
            $table->dropColumn([
                'name',
                'original_name',
                'description',
                'instructions',
                'video_url',
                'image',
                'muscle_groups',
                'equipment',
                'form_cues',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_plan_exercises', function (Blueprint $table) {
            $table->string('name')->after('order');
            $table->string('original_name')->nullable()->after('name');
            $table->text('description')->nullable()->after('type');
            $table->json('instructions')->nullable()->after('description');
            $table->string('video_url')->nullable()->after('instructions');
            $table->string('image')->nullable()->after('video_url');
            $table->json('muscle_groups')->nullable()->after('weight_recommendation');
            $table->json('equipment')->nullable()->after('muscle_groups');
            $table->text('form_cues')->nullable()->after('equipment');

            $table->index('original_name', 'exercises_original_name_index');
        });
    }
};
