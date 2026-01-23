<?php

namespace App\Helpers;

use OpenAI\Responses\Responses\CreateResponse;
use App\Exceptions\OpenAIToolCallException;

class ToolCallHelper
{
    public static function extractToolCall(
        CreateResponse $response,
        string $expectedFunction,
        ?callable $validator = null
    ): array {
        if (empty($response->output)) {
            throw OpenAIToolCallException::noOutput();
        }

        $functionCall = collect($response->output)
            ->firstWhere('type', 'function_call');

        if (!$functionCall) {
            throw OpenAIToolCallException::noFunctionCall();
        }

        if ($functionCall->name !== $expectedFunction) {
            throw OpenAIToolCallException::unexpectedFunction(
                $expectedFunction,
                $functionCall->name
            );
        }

        $arguments = json_decode($functionCall->arguments, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw OpenAIToolCallException::invalidJson(json_last_error_msg());
        }

        // Optional custom validation
        if ($validator && !$validator($arguments)) {
            throw OpenAIToolCallException::invalidArguments('Custom validation failed');
        }

        return $arguments;
    }

    /**
     * Extract multiple tool calls from the OpenAI Responses API output
     *
     * @param CreateResponse $response The OpenAI response
     * @param string|null $expectedFunction Optional: filter by specific function name
     * @param callable|null $validator Optional validation function for each tool call
     * @return array Array of decoded arguments from all matching tool calls
     * @throws OpenAIToolCallException
     */
    public static function extractToolCalls(
        CreateResponse $response,
        ?string $expectedFunction = null,
        ?callable $validator = null
    ): array {
        if (empty($response->output)) {
            throw OpenAIToolCallException::noOutput();
        }

        $functionCalls = collect($response->output)
            ->filter(fn($item) => $item->type === 'function_call')
            ->when($expectedFunction, fn($collection) =>
                $collection->filter(fn($item) => $item->name === $expectedFunction)
            );

        if ($functionCalls->isEmpty()) {
            throw OpenAIToolCallException::noFunctionCall();
        }

        $results = [];
        foreach ($functionCalls as $functionCall) {
            $arguments = json_decode($functionCall->arguments, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw OpenAIToolCallException::invalidJson(json_last_error_msg());
            }

            // Optional custom validation
            if ($validator && !$validator($arguments)) {
                throw OpenAIToolCallException::invalidArguments('Custom validation failed');
            }

            $results[] = $arguments;
        }

        return $results;
    }
}
