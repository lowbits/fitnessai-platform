<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Models\AppFeedback;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Captures what the user wishes the app could do — a feature request, a bug, or
 * general feedback — so a real ask is recorded instead of hitting a dead
 * "coming soon". Read-write: it stores one row and confirms it.
 */
class SubmitFeedbackTool implements Tool
{
    private const TYPES = ['feature_request', 'bug', 'general'];

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Records the user\'s feature request, bug report or general feedback about the app. Use it when they wish the app could do something you have no tool for ("kannst du auch mein Training umplanen?", "es wäre cool wenn…", "das ist ein Bug"), and only after they agree to pass it on. Pass type (feature_request|bug|general) and message (their wish in their own words). Confirm warmly once it is saved.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()
                ->description('feature_request, bug or general. Defaults to feature_request.'),
            'message' => $schema->string()
                ->description('The user\'s feedback in their own words — what they want or what went wrong.')
                ->required(),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $message = trim((string) ($request['message'] ?? ''));

        if ($message === '') {
            return ToolResult::error('empty_feedback', 'Ask the user what they would like to pass on before saving.');
        }

        $type = (string) ($request['type'] ?? 'feature_request');
        if (! in_array($type, self::TYPES, true)) {
            $type = 'feature_request';
        }

        AppFeedback::create([
            'user_id' => $this->user->id,
            'type' => $type,
            'message' => $message,
            'context' => ['locale' => $this->user->locale ?? 'en'],
        ]);

        return ToolResult::info('feedback_received', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}
