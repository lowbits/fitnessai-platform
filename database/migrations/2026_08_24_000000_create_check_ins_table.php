<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('body_progress_id')->nullable()
                ->constrained('body_progress')->nullOnDelete()
                ->comment('The weigh-in row this check-in produced, if any');
            $table->unsignedTinyInteger('mood')->nullable()->comment('1 (rough) to 5 (great)');
            $table->unsignedTinyInteger('energy')->nullable()->comment('1 (very low) to 5 (very high)');
            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->index(['user_id', 'checked_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
