<?php

namespace App\Ai\Tools\Support;

/**
 * Builds the two JSON envelopes every Mona tool returns, so the shape stays
 * consistent across tools and the contract lives in one place:
 *  - widget: rendered by the mobile client, keyed by widget name
 *  - error:  a typed signal Mona surfaces as text (never rendered)
 */
final class ToolResult
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function widget(string $name, array $data): string
    {
        return json_encode([
            'widget' => $name,
            'requires_input' => true,
            'data' => $data,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    public static function error(string $code, string $message, array $extra = []): string
    {
        return json_encode(['error' => $code, 'message' => $message, ...$extra]);
    }
}
