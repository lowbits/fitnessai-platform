<?php

namespace App\Ai;

use App\Models\User;
use Laravel\Ai\Responses\AgentResponse;

/**
 * A single assistant message in the coach contract: an optional conversation id
 * plus a list of parts (text | widget). Built from a real Mona reply, or as a
 * synthetic message such as the premium upsell.
 */
final class CoachMessage
{
    /**
     * @param  array<int, array<string, mixed>>  $parts
     */
    private function __construct(
        public readonly ?string $conversationId,
        public readonly array $parts,
    ) {}

    public static function fromAgent(AgentResponse $response): self
    {
        return new self($response->conversationId, self::partsFromAgent($response));
    }

    /**
     * A free user gets a friendly upsell rendered as a chat widget (not an
     * error), so the client can open the paywall in place.
     */
    public static function upsell(User $user): self
    {
        return new self(null, [
            ['type' => 'text', 'text' => __('coach.upsell', [], $user->locale ?? 'en')],
            ['type' => 'widget', 'name' => 'upsell', 'requires_input' => false, 'data' => ['cta' => 'start_trial']],
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function partsFromAgent(AgentResponse $response): array
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
