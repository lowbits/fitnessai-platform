<?php

namespace App\Ai\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Records a bodyweight the user reports in chat ("ich habe mich heute morgen
 * gewogen, 103 kg") into body_progress — the same store the weight trend chart
 * reads. Returns the change vs start and vs last entry so Mona can reflect on
 * the trend instead of just confirming a number.
 */
class LogWeightTool implements Tool
{
    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Records the user\'s current bodyweight in their progress when they report it in chat (e.g. "ich habe mich heute gewogen, 103 kg"). Pass weight_kg. This is the weekly check-in — it feeds the weight trend chart.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'weight_kg' => $schema->number()
                ->description('The user\'s bodyweight in kilograms, as they reported it.')
                ->required(),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $weight = (float) ($request['weight_kg'] ?? 0);

        if ($weight < 20 || $weight > 500) {
            return json_encode([
                'error' => 'invalid_weight',
                'message' => 'That weight looks off — ask the user to confirm their weight in kg.',
            ]);
        }

        $startWeight = $this->user->profile?->weight_kg
            ?? $this->user->bodyProgress()->orderBy('recorded_at')->value('weight_kg');
        $previousWeight = $this->user->bodyProgress()->orderByDesc('recorded_at')->value('weight_kg');

        $this->user->bodyProgress()->create([
            'weight_kg' => $weight,
            'recorded_at' => now(),
        ]);

        Log::debug('[Coach][LogWeight] Recorded', [
            'user_id' => $this->user->id,
            'weight_kg' => $weight,
        ]);

        return json_encode([
            'logged' => true,
            'current_weight' => $weight,
            'start_weight' => $startWeight !== null ? (float) $startWeight : null,
            'previous_weight' => $previousWeight !== null ? (float) $previousWeight : null,
            'change_since_start' => $startWeight !== null ? round($weight - (float) $startWeight, 1) : null,
            'change_since_last' => $previousWeight !== null ? round($weight - (float) $previousWeight, 1) : null,
        ]);
    }
}
