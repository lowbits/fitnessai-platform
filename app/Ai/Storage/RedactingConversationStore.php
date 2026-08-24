<?php

namespace App\Ai\Storage;

use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Storage\DatabaseConversationStore;

class RedactingConversationStore extends DatabaseConversationStore
{
    public function storeUserMessage(
        string $conversationId,
        ?string $participantType,
        string|int|null $participantId,
        AgentPrompt $prompt
    ): string {
        return parent::storeUserMessage(
            $conversationId,
            $participantType,
            $participantId,
            $prompt->attachments->isEmpty() ? $prompt : $prompt->revise($prompt->prompt, []),
        );
    }
}
