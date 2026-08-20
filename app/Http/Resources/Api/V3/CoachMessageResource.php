<?php

namespace App\Http\Resources\Api\V3;

use App\Ai\CoachMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes a CoachMessage into the contract the app renders:
 *   { conversation_id, message: { role, parts: CoachPart[] } }
 *
 * CoachPart is a discriminated union (text | widget), so a new tool only needs a
 * client renderer keyed by its widget name.
 *
 * @mixin CoachMessage
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
        /** @var CoachMessage $message */
        $message = $this->resource;

        return [
            'conversation_id' => $message->conversationId,
            'message' => [
                'role' => 'assistant',
                'parts' => $message->parts,
            ],
        ];
    }
}
