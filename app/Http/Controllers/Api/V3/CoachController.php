<?php

namespace App\Http\Controllers\Api\V3;

use App\Ai\Agents\MonaCoachAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\CoachMessageRequest;
use App\Http\Resources\Api\V3\CoachMessageResource;
use App\Models\Meal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;

/**
 * Mona chat — one message in, one assistant reply out (request/response).
 *
 * The reply is serialized to the message-part contract (text + tool_result +
 * suggestions) so the client renders every tool the same way. Conversation
 * history is persisted by the agent (RemembersConversations).
 */
class CoachController extends Controller
{
    public function __invoke(CoachMessageRequest $request): CoachMessageResource
    {
        $user = $request->user();

        $meal = null;
        if ($request->input('context.type') === 'meal_replace') {
            $meal = Meal::findOrFail($request->integer('context.meal_id'));
            Gate::authorize('update', $meal);
        }

        $agent = new MonaCoachAgent($user, $meal);

        $conversationId = $request->input('conversation_id');

        if ($conversationId) {
            $owns = DB::table('agent_conversations')
                ->where('id', $conversationId)
                ->where('participant_type', $user::class)
                ->where('participant_id', $user->id)
                ->exists();

            abort_unless($owns, 403);

            $agent->continue($conversationId, as: $user);
        } else {
            $agent->forUser($user);
        }

        $response = $agent->prompt(
            (string) $request->string('message'),
            provider: [Lab::OpenAI],
            model: config('ai.models.agent'),
        );

        Log::debug('[Coach] Agent replied', [
            'user_id' => $user->id,
            'has_meal_context' => (bool) $meal,
            'meal_id' => $meal?->id,
            'text' => $response->text,
            'tool_calls' => $response->toolCalls
                ->map(fn ($c) => ['name' => $c->name, 'arguments' => $c->arguments])
                ->all(),
            'tool_results' => $response->toolResults
                ->map(fn ($r) => [
                    'name' => $r->name,
                    'result' => is_string($r->result) ? $r->result : json_encode($r->result),
                ])
                ->all(),
        ]);

        return new CoachMessageResource($response);
    }
}
