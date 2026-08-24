<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->longText('content')->change();
            $table->longText('attachments')->change();
            $table->longText('tool_calls')->change();
            $table->longText('tool_results')->change();
        });
    }

    public function down(): void
    {
        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->text('content')->change();
            $table->text('attachments')->change();
            $table->text('tool_calls')->change();
            $table->text('tool_results')->change();
        });
    }
};
