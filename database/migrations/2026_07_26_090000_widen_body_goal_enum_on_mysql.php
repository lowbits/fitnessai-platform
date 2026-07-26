<?php

use App\Enums\BodyGoal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MySQL-only catch-up. `create_user_profiles` made body_goal a MySQL ENUM
     * frozen at the goals that existed then; the migration that added the newer
     * goals (muscle_gain, weight_loss, …) was Postgres-only (a CHECK constraint),
     * so MySQL would reject those values at runtime. Postgres already allows them;
     * SQLite (tests) doesn't enforce enums — both are skipped here.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        if (! Schema::hasColumn('user_profiles', 'body_goal')) {
            return;
        }

        $values = collect(BodyGoal::cases())
            ->map(fn (BodyGoal $goal) => "'".$goal->value."'")
            ->implode(', ');

        DB::statement("ALTER TABLE user_profiles MODIFY body_goal ENUM({$values}) NULL");
    }

    public function down(): void
    {
        // Not reversible: narrowing the ENUM could reject existing rows.
    }
};
