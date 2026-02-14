<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Log;
use Meilisearch\Client;
use Stringable;

class MeilisearchSimilaritySearch implements Tool
{
    public function __construct(private readonly Client $client) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search for exercises in the database by name, muscle group, equipment, movement type, or any descriptive criteria. Use this whenever the user asks about specific exercises, wants alternatives, or needs exercises matching certain criteria.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {

        Log::debug('Calling MeilisearchSimilaritySearch with request: '.json_encode($request->all()));

        $results = $this->client->index('exercises')
            ->search($request['query'], [
                'hybrid' => [
                    'semanticRatio' => 0.75,
                    'embedder' => 'default',
                ],
                'limit' => $request['limit'],
            ]);

        $hits = collect($results->getHits())->map(function ($hit) {
            return [
                'id' => $hit['id'],
                'name' => $hit['name'],
                'type' => $hit['type'],
                'description' => $hit['description'] ?? null,
            ];
        });

        return json_encode($hits);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Search query describing the exercise, muscle group, movement pattern, or type')->required(),
            'limit' => $schema->integer()->description('Maximum number of results to return')->default(5),
        ];
    }
}
