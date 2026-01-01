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
        Schema::create('workout_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workout_plan_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('feeling_rate')->nullable()->comment('Rate from 1-5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_trackings');
    }
};

