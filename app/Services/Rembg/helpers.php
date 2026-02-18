<?php

use App\Services\Rembg\PhotoroomClient;

if (! function_exists('rembg')) {
    /**
     * Get a RembgClient instance.
     */
    function rembg(): PhotoroomClient
    {
        return app(PhotoroomClient::class);
    }
}
