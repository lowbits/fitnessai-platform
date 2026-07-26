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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->nullable()->after('remember_token');
        });

        // Set trial_ends_at for existing users based on their created_at
        $driver = Schema::getConnection()->getDriverName();
        $expression = match ($driver) {
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
