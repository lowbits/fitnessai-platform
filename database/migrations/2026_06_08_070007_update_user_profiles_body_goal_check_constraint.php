<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT user_profiles_body_goal_check');
        DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_body_goal_check CHECK (body_goal::text = ANY (ARRAY['lose_weight', 'build_muscle', 'get_fit', 'muscle_gain', 'weight_loss', 'maintenance', 'endurance', 'strength']::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT user_profiles_body_goal_check');
        DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_body_goal_check CHECK (body_goal::text = ANY (ARRAY['muscle_gain', 'weight_loss', 'maintenance', 'endurance', 'strength']::text[]))");
    }
};
