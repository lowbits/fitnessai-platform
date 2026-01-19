<?php

use App\Enums\DietaryPreference;
use App\Enums\DietStyle;
use App\Enums\UserSource;
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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->enum('dietary_preference', array_column(DietaryPreference::cases(), 'value'))->nullable()->after('diet_type');
            $table->enum('diet_style', array_column(DietStyle::cases(), 'value'))->nullable()->after('dietary_preference');
            $table->json('training_days')->nullable()->after('training_sessions_per_week');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('source', array_column(UserSource::cases(), 'value'))->default(UserSource::WEB->value)->after('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['dietary_preference', 'diet_style', 'training_days']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
