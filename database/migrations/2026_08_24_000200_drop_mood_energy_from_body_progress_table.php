<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_progress', function (Blueprint $table) {
            $table->dropColumn(['mood', 'energy']);
        });
    }

    public function down(): void
    {
        Schema::table('body_progress', function (Blueprint $table) {
            $table->unsignedTinyInteger('mood')->nullable()->after('notes')->comment('Weekly check-in mood, 1 (rough) to 5 (great)');
            $table->unsignedTinyInteger('energy')->nullable()->after('mood')->comment('Weekly check-in energy, 1 (very low) to 5 (very high)');
        });
    }
};
