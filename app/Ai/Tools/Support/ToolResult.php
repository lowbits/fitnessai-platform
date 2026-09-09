<?php

namespace App\Ai\Tools\Support;

/**
 * Builds the JSON envelopes every Mona tool returns, so the shape stays
 * consistent across tools and the contract lives in one place:
 *  - widget: an interactive card the user acts on (requires_input)
 *  - info:   a read-only card that just shows state (no input expected)
 *  - data:   plain result with no card — Mona reads it and replies in text
 *  - error:  a typed signal Mona surfaces as text (never rendered)
 */
final class ToolResult
{
    /**
     * A backend action with no card (saving a preference, rebuilding a plan).
     * Has no `widget` key, so it is never rendered — Mona verbalizes the result.
     *
     * @param  array<string, mixed>  $data
     */
    public static function data(array $data): string
    {
        return json_encode($data);
    }

    /**
     * An interactive widget the user is expected to act on (pick a meal, swap,
     * add). The client renders it keyed by name.
     *
     * @param  array<string, mixed>  $data
     */
    public static function widget(string $name, array $data): string
    {
        return self::envelope($name, $data, requiresInput: true);
    }

    /**
     * A read-only widget that just surfaces state (today's workout, calorie
     * status). Same render seam, but nothing is asked of the user.
     *
     * @param  array<string, mixed>  $data
     */
    public static function info(string $name, array $data): string
    {
        return self::envelope($name, $data, requiresInput: false);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function envelope(string $name, array $data, bool $requiresInput): string
    {
        return json_encode([
            'widget' => $name,
            'requires_input' => $requiresInput,
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
