<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guarded: MySQL auto-commits the ADD COLUMN, so a re-run after a failed
        // backfill would otherwise trip over the already-existing column.
        if (! Schema::hasColumn('users', 'trial_ends_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('trial_ends_at')->nullable()->after('remember_token');
            });
        }

        // Set trial_ends_at for existing users based on their created_at
        $driver = Schema::getConnection()->getDriverName();
        $expression = match ($driver) {
            'mysql', 'mariadb' => 'DATE_ADD(created_at, INTERVAL 7 DAY)',
            'pgsql' => "created_at + interval '7 days'",
            default => "datetime(created_at, '+7 days')",
        };

        DB::table('users')
            ->whereNull('trial_ends_at')
            ->update(['trial_ends_at' => DB::raw($expression)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('trial_ends_at');
        });
    }
};
