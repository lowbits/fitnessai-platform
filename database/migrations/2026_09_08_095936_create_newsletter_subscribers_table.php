<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('country', 2)->nullable();
            $table->string('platform')->nullable();
            $table->string('source')->nullable();
            $table->text('consent_text')->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('resend_contact_id')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
