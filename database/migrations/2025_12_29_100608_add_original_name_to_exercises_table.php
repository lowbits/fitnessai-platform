<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('name');
            $table->index('original_name');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropIndex(['original_name']);
            $table->dropColumn('original_name');
        });
    }
};
