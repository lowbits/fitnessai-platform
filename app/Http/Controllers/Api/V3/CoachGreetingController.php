<?php

namespace App\Http\Controllers\Api\V3;

use App\Ai\Support\CoachGreeting;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoachGreetingController extends Controller
{
    public function __construct(private readonly CoachGreeting $greeting) {}

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->greeting->textFor($request->user()),
        ]);
    }
}
