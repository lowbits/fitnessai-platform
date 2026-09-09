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
        Schema::create('plan_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('score');
            $table->string('plan_type')->default('workout');
            $table->string('source');
            $table->json('facts');
            $table->json('dimensions');
            $table->longText('plan_text')->nullable();
            $table->string('tone');
            $table->string('locale', 5);
            $table->timestamps();

            $table->index('score');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_evaluations');
    }
};
