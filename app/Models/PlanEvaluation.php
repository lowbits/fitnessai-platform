<?php

namespace App\Models;

use Database\Factories\PlanEvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A stored plan-roast result: the score, the extracted facts and the scored
 * dimensions. Kept so we can validate the tool over time and tell a user how
 * their plan compares to others.
 */
class PlanEvaluation extends Model
{
    /** @use HasFactory<PlanEvaluationFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'score',
        'plan_type',
        'source',
        'facts',
        'dimensions',
        'plan_text',
        'tone',
        'locale',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'facts' => 'array',
            'dimensions' => 'array',
        ];
    }

    /**
     * Share of stored plans this score beats, plus how many we compared against.
     *
     * @return array{percentile: int, sample_size: int}
     */
    public static function ranking(int $score): array
    {
        $sampleSize = static::query()->count();
        $beaten = static::query()->where('score', '<', $score)->count();

        return [
            'percentile' => $sampleSize > 0 ? (int) round($beaten / $sampleSize * 100) : 0,
            'sample_size' => $sampleSize,
        ];
    }
}
