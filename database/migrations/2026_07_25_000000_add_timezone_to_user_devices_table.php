<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_devices', function (Blueprint $table) {
            // IANA zone (e.g. "Europe/Berlin") so reminders fire in the user's local time.
            $table->string('timezone')->nullable()->after('platform');
            // BCP-47 tag (e.g. "de-DE") for localized push copy.
            $table->string('locale')->nullable()->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_devices', function (Blueprint $table) {
            $table->dropColumn(['timezone', 'locale']);
        });
    }
};
