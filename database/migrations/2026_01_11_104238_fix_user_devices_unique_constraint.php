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
            // Drop the old global unique constraint on device_id
            $table->dropUnique('user_devices_device_id_unique');

            // Add composite unique constraint for user_id + device_id
            $table->unique(['user_id', 'device_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_devices', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique(['user_id', 'device_id']);

            // Restore the old global unique constraint (this may fail if duplicate device_ids exist)
            $table->unique('device_id');
        });
    }
};

