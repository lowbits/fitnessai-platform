<?php

namespace App\Http\Controllers;

use App\Actions\PlanEvaluator\EvaluatePlan;
use App\Ai\Agents\PlanVerdictAgent;
use App\Models\PlanEvaluation;
use App\Support\PlanEvaluator\Dimension;
use App\Support\PlanEvaluator\PlanTextExtractor;
use App\Support\PlanEvaluator\VerdictPrompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Ai\Responses\StreamableAgentResponse;

class PlanRoastController extends Controller
{
    private const CACHE_TTL_MINUTES = 15;

    public function __construct(
        private readonly EvaluatePlan $evaluatePlan,
        private readonly PlanTextExtractor $planText,
    ) {}

    public function index(): Response
    {
        $locale = app()->getLocale();

        return Inertia::render('FreeTools/PlanRoast', [
            'meta' => fn () => $this->meta($locale),
            'schema' => fn () => $this->buildSchema($locale),
            'sources' => fn () => trans('plan_roast.sources'),
            'author' => fn () => $this->author($locale),
            'internalLinks' => fn () => $this->internalLinks($locale),
            'relatedArticles' => fn () => $this->relatedArticles($locale),
        ]);
    }

    public function evaluate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan' => ['required_without:files', 'nullable', 'string', 'max:20000', Rule::when(! $request->hasFile('files'), ['min:20'])],
            'files' => ['required_without:plan', 'array', 'max:6'],
            'files.*' => ['file', 'max:10240', 'mimes:txt,pdf,jpg,jpeg,png,webp'],
            'tone' => ['nullable', 'in:roast,neutral'],
            'locale' => ['nullable', 'in:de,en'],
        ]);

        $resolved = $this->resolve($request, $data);
        $evaluation = $resolved['evaluation'];

        if (($evaluation['plan_type'] ?? 'unknown') !== 'workout') {
            return response()->json(['plan_type' => $evaluation['plan_type'] ?? 'unknown']);
        }

        $locale = $data['locale'] ?? app()->getLocale();
        $tone = $data['tone'] ?? 'roast';

        PlanEvaluation::create([
            'score' => $evaluation['score'],
            'plan_type' => 'workout',
            'source' => $resolved['source'],
            'facts' => $evaluation['facts'],
            'dimensions' => $this->dimensionsToArray($evaluation['dimensions']),
            'plan_text' => $resolved['plan_text'],
            'tone' => $tone,
            'locale' => $locale,
        ]);

        $token = (string) Str::uuid();

        Cache::put("plan_roast:{$token}", [
            'evaluation' => $evaluation,
            'tone' => $tone,
            'locale' => $locale,
            'context' => $resolved['context'],
        ], now()->addMinutes(self::CACHE_TTL_MINUTES));

        return response()->json([
            'plan_type' => 'workout',
            'score' => $evaluation['score'],
            'dimensions' => $this->presentDimensions($evaluation['dimensions'], $locale),
            'ranking' => PlanEvaluation::ranking($evaluation['score']),
            'stream_token' => $token,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{evaluation: array<string, mixed>, source: string, plan_text: ?string, context: string}
     */
    private function resolve(Request $request, array $data): array
    {
        $message = trim((string) ($data['plan'] ?? ''));

        /** @var list<UploadedFile> $files */
        $files = array_values($request->file('files', []));

        if ($files === []) {
            return ['evaluation' => $this->evaluatePlan->forText($message), 'source' => 'paste', 'plan_text' => $message, 'context' => $message];
        }

        $images = array_values(array_filter($files, fn (UploadedFile $file) => str_starts_with((string) $file->getMimeType(), 'image/')));

        if ($images !== []) {
            return ['evaluation' => $this->evaluatePlan->forImages($images, $message), 'source' => 'image', 'plan_text' => $message ?: null, 'context' => $message];
        }

        $documentText = collect($files)->map(fn (UploadedFile $file) => $this->planText->fromUpload($file))->implode("\n\n");
        $text = trim($message === '' ? $documentText : $message."\n\n".$documentText);
        $source = strtolower((string) $files[0]->getClientOriginalExtension()) === 'pdf' ? 'pdf' : 'txt';

        return ['evaluation' => $this->evaluatePlan->forText($text), 'source' => $source, 'plan_text' => $text, 'context' => $message];
    }

    /**
     * @param  list<Dimension>  $dimensions
     * @return list<array{key: string, rating: string, context: array<string, mixed>}>
     */
    private function dimensionsToArray(array $dimensions): array
    {
        return collect($dimensions)->map(fn (Dimension $dimension) => [
            'key' => $dimension->key,
            'rating' => $dimension->rating->value,
            'context' => $dimension->context,
        ])->all();
    }

    public function stream(string $token): StreamableAgentResponse
    {
        $cached = Cache::get("plan_roast:{$token}");

        abort_if($cached === null, 404);

        return (new PlanVerdictAgent)->stream(
            VerdictPrompt::for($cached['evaluation'], $cached['tone'], $cached['locale'], $cached['context'] ?? '')
        );
    }

    /**
     * @param  list<Dimension>  $dimensions
     * @return list<array{key: string, rating: string, label: string, evidence: string}>
     */
    private function presentDimensions(array $dimensions, string $locale): array
    {
        return collect($dimensions)->map(fn (Dimension $dimension) => [
            'key' => $dimension->key,
            'rating' => $dimension->rating->value,
            'label' => trans("plan_roast.dimensions.{$dimension->key}.label", [], $locale),
            'evidence' => trans("plan_roast.dimensions.{$dimension->key}.evidence", [], $locale),
        ])->all();
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogImage: string, ogImageAlt: string}
     */
    private function meta(string $locale): array
    {
        $baseUrl = config('app.url');
        $path = trans('routes.free_tools_plan_roast', [], $locale);

        return [
            'title' => trans('plan_roast.meta.title'),
            'description' => trans('plan_roast.meta.description'),
            'canonical' => "{$baseUrl}/{$locale}/{$path}",
            'ogImage' => "{$baseUrl}/assets/images/og/plan-roast.webp",
            'ogImageAlt' => trans('plan_roast.meta.og_image_alt'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildSchema(string $locale): array
    {
        $baseUrl = config('app.url');
        $url = "{$baseUrl}/{$locale}/".trans('routes.free_tools_plan_roast', [], $locale);

        $webApp = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => trans('plan_roast.schema.name'),
            'url' => $url,
            'applicationCategory' => 'HealthApplication',
            'operatingSystem' => 'All',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR'],
            'description' => trans('plan_roast.meta.description'),
            'inLanguage' => $locale,
            'dateModified' => trans('plan_roast.reviewed_date'),
            'citation' => array_map(fn (array $source) => [
                '@type' => 'ScholarlyArticle',
                'name' => $source['title'],
                'author' => $source['authors'],
                'datePublished' => $source['year'],
                'url' => $source['url'],
            ], trans('plan_roast.sources')),
        ];

        $steps = trans('plan_roast.howto.steps');
        $howTo = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => trans('plan_roast.howto.name'),
            'description' => trans('plan_roast.howto.description'),
            'step' => array_map(fn (array $step, int $i) => [
                '@type' => 'HowToStep',
                'position' => $i + 1,
                'name' => $step['name'],
                'text' => $step['text'],
                'url' => "{$url}#how-it-works",
            ], $steps, array_keys($steps)),
        ];

        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
            ], trans('plan_roast.faqs')),
        ];

        return [$webApp, $howTo, $faqSchema];
    }

    /**
     * @return array<string, string>
     */
    private function author(string $locale): array
    {
        $author = config("blog.default_author.{$locale}", []);

        if (isset($author['image'])) {
            $author['image'] = url($author['image']);
        }

        return $author;
    }

    /**
     * @return array<int, array{id: string, url: string}>
     */
    private function internalLinks(string $locale): array
    {
        return [
            ['id' => 'calorie', 'url' => "/{$locale}/".trans('routes.free_tools_calorie_calculator', [], $locale)],
            ['id' => 'macro', 'url' => "/{$locale}/".trans('routes.free_tools_macro_calculator', [], $locale)],
            ['id' => 'workoutPlans', 'url' => "/{$locale}/".trans('routes.workout_plans_index', [], $locale)],
        ];
    }

    /**
     * @return array<int, array{url: string, title: string, description: string}>
     */
    private function relatedArticles(string $locale): array
    {
        $related = [];

        foreach (config("blog.{$locale}", []) as $slug => $article) {
            $related[] = [
                'url' => "/{$locale}/blog/{$slug}",
                'title' => $article['h1'],
                'description' => $article['description'],
            ];
        }

        return $related;
    }
}
