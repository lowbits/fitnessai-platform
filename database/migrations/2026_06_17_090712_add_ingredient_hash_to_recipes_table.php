<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('ingredient_hash', 40)->nullable()->after('source_locale');
            $table->index(['ingredient_hash', 'source_locale']);
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['ingredient_hash', 'source_locale']);
            $table->dropColumn('ingredient_hash');
        });
    }
};
