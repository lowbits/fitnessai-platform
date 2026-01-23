<?php


namespace App\Exceptions;

class OpenAIToolCallException extends \RuntimeException
{
    public static function noOutput(): self
    {
        return new self('No output returned from OpenAI');
    }

    public static function noFunctionCall(): self
    {
        return new self('No function call returned from OpenAI');
    }

    public static function unexpectedFunction(string $expected, string $actual): self
    {
        return new self("Expected function '{$expected}', got '{$actual}'");
    }

    public static function invalidJson(string $error): self
    {
        return new self("Invalid JSON in function arguments: {$error}");
    }

    public static function invalidArguments(string $message): self
    {
        return new self($message);
    }
}
