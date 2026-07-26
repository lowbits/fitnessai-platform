<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Ai\Responses\AgentResponse;

/**
 * Serializes an AgentResponse into the message-part contract the app renders:
 *   { conversation_id, message: { role, parts: CoachPart[] } }
 *
 * CoachPart is a discriminated union — text | tool_result | suggestions — so a
 * new tool only needs a client renderer keyed by its `tool` name.
 *
 * @mixin AgentResponse
 */
class CoachMessageResource extends JsonResource
{
    /**
     * Flat contract — no "data" envelope.
     */
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var AgentResponse $response */
        $response = $this->resource;

        return [
            'conversation_id' => $response->conversationId,
            'message' => [
                'role' => 'assistant',
                'parts' => $this->parts($response),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parts(AgentResponse $response): array
    {
        $parts = [];

        if (filled($response->text)) {
            $parts[] = ['type' => 'text', 'text' => $response->text];
        }

        foreach ($response->toolResults as $result) {
            $envelope = $result->result;

            // Tools return a JSON string from handle(); decode to the widget
            // envelope { widget, requires_input, data }.
            if (is_string($envelope)) {
                $decoded = json_decode($envelope, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $envelope = $decoded;
                }
            }

            // Only widget-producing tool results become parts; anything else
            // (e.g. an error result) is left to Mona's text.
            if (! is_array($envelope) || ! isset($envelope['widget'])) {
                continue;
            }

            $parts[] = [
                'type' => 'widget',
                'name' => $envelope['widget'],
                'data' => $envelope['data'] ?? null,
                'requires_input' => (bool) ($envelope['requires_input'] ?? false),
            ];
        }

        return $parts;
    }
}
