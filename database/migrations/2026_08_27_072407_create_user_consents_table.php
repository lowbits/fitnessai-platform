<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type', 32);
            $table->string('version', 32);
            $table->timestamp('granted_at');
            $table->timestamp('revoked_at')->nullable();
            $table->string('source', 32);
            $table->string('locale', 5);
            $table->timestamps();

            $table->index(['user_id', 'consent_type', 'revoked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_consents');
    }
};
