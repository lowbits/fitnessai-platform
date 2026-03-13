<?php

namespace App\Services\Rembg;

use Illuminate\Support\Facades\Http;
use Log;
use RuntimeException;

class PhotoroomClient
{
    private string $apiKey;

    private int $timeout;

    public function __construct(?string $apiKey = null, int $timeout = 30)
    {
        $this->apiKey = $apiKey ?? config('services.photoroom.api_key');
        $this->timeout = $timeout;
    }

    public function remove(string $image): string
    {
        Log::debug('[PhotoroomClient] Removing background');

        $response = Http::timeout($this->timeout)
            ->withHeaders(['x-api-key' => $this->apiKey])
            ->attach('image_file', $image, 'image.webp')
            ->post('https://sdk.photoroom.com/v1/segment', [
                'format' => 'webp',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                "Photoroom API returned HTTP {$response->status()}: {$response->body()}"
            );
        }

        return $response->body();
    }
}
