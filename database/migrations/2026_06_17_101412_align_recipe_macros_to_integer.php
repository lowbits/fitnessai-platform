<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['protein_g', 'carbs_g', 'fat_g', 'fiber_g', 'sugar_g'] as $col) {
            DB::statement("ALTER TABLE recipes ALTER COLUMN {$col} TYPE integer USING ROUND({$col})::integer");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['protein_g', 'carbs_g', 'fat_g', 'fiber_g', 'sugar_g'] as $col) {
            DB::statement("ALTER TABLE recipes ALTER COLUMN {$col} TYPE numeric(8,2) USING {$col}::numeric(8,2)");
        }
    }
};
