<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\PlanWeekRequest;
use App\Services\PlanWeekService;
use Illuminate\Http\JsonResponse;

class PlanWeekController extends Controller
{
    public function __construct(private readonly PlanWeekService $service) {}

    public function __invoke(PlanWeekRequest $request): JsonResponse
    {
        $user = $request->user();
        $plan = $user->plans()->where('status', 'active')->firstOrFail();

        return response()->json($this->service->build($user, $plan, $request->anchorDate()));
    }
}
