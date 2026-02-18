<?php

namespace App\Services\Rembg;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Log;
use RuntimeException;

class RembgClient
{
    private string $baseUrl;

    private int $timeout;

    private string $model;

    public function __construct(
        ?string $baseUrl = null,
        int $timeout = 60,
        string $model = 'isnet-general-use',
    ) {
        $this->baseUrl = rtrim($baseUrl ?? config('services.rembg.url'), '/');
        $this->timeout = $timeout;
        $this->model = $model;
    }

    /**
     * Remove the background from an image.
     *
     * @param  string  $image  Raw image bytes
     * @return string Raw PNG bytes with transparent background
     */
    public function remove(string $image): string
    {

        $params = http_build_query([
            'model' => $this->model,
            'a' => true,
            'ae' => '0',
            'ppm' => true,
        ]);

        Log::debug('[RembgClient] Removing background from image', [
            'params' => $params,
        ]);

        $response = $this->request()
            ->attach('file', $image, 'image.png')
            ->post("{$this->baseUrl}/api/remove", [
                'model' => $this->model,
                'a' => true,
                'ae' => '0',
                'ppm' => true,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                "rembg API returned HTTP {$response->status()}: {$response->body()}"
            );
        }

        return $response->body();
    }

    /**
     * Set the model to use for background removal.
     */
    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set the request timeout in seconds.
     */
    public function timeout(int $seconds): static
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Build the base HTTP request.
     */
    private function request(): PendingRequest
    {
        return Http::timeout($this->timeout);
    }
}
