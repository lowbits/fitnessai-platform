<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (test DB) has no ENUM constraint — the column is TEXT and accepts 'flex' as-is.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE meals MODIFY type ENUM('breakfast', 'lunch', 'snack', 'dinner', 'flex') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE meals SET type = 'snack' WHERE type = 'flex'");
        DB::statement("ALTER TABLE meals MODIFY type ENUM('breakfast', 'lunch', 'snack', 'dinner') NOT NULL");
    }
};
