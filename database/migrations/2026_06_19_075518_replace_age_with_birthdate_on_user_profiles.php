<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->after('age');
        });

        DB::table('user_profiles')
            ->whereNotNull('age')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('user_profiles')
                        ->where('id', $row->id)
                        ->update(['birthdate' => Carbon::now()->subYears((int) $row->age)->subMonths(6)->startOfDay()]);
                }
            });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('age');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('age')->nullable()->after('user_id');
        });

        DB::table('user_profiles')
            ->whereNotNull('birthdate')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('user_profiles')
                        ->where('id', $row->id)
                        ->update(['age' => Carbon::parse($row->birthdate)->age]);
                }
            });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('birthdate');
        });
    }
};
