<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * laravel/ai 0.11 moved conversation ownership from a `user_id` column to a
 * polymorphic participant (participant_type/participant_id) and added
 * `approval_state` to messages. Migrate existing rows to the new shape.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->string('participant_type')->nullable()->after('id');
            $table->unsignedBigInteger('participant_id')->nullable()->after('participant_type');
        });

        DB::table('agent_conversations')->update([
            'participant_type' => User::class,
            'participant_id' => DB::raw('user_id'),
        ]);

        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'updated_at']);
            $table->dropColumn('user_id');
            $table->index(['participant_type', 'participant_id', 'updated_at'], 'participant_updated_at_index');
        });

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->string('participant_type')->nullable()->after('conversation_id');
            $table->unsignedBigInteger('participant_id')->nullable()->after('participant_type');
            $table->text('approval_state')->nullable()->after('meta');
        });

        DB::table('agent_conversation_messages')->update([
            'participant_type' => User::class,
            'participant_id' => DB::raw('user_id'),
        ]);

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->dropIndex('conversation_index');
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
            $table->index(['conversation_id', 'participant_type', 'participant_id', 'updated_at'], 'conversation_index');
            $table->index(['participant_type', 'participant_id'], 'participant_index');
        });
    }

    public function down(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id');
        });

        DB::table('agent_conversations')->update(['user_id' => DB::raw('participant_id')]);

        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropIndex('participant_updated_at_index');
            $table->dropColumn(['participant_type', 'participant_id']);
            $table->index(['user_id', 'updated_at']);
        });

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('conversation_id');
        });

        DB::table('agent_conversation_messages')->update(['user_id' => DB::raw('participant_id')]);

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->dropIndex('conversation_index');
            $table->dropIndex('participant_index');
            $table->dropColumn(['participant_type', 'participant_id', 'approval_state']);
            $table->index(['conversation_id', 'user_id', 'updated_at'], 'conversation_index');
            $table->index(['user_id']);
        });
    }
};
