<?php

namespace App\Ai\Consent;

use Exception;
use Illuminate\Http\JsonResponse;

class ConsentRequiredException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'consent_required',
            'code' => 'consent_required',
        ], 409);
    }
}
