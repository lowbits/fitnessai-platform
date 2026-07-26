<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\PlanDayRequest;
use App\Http\Resources\Api\V3\DayResource;
use App\Services\PlanDayService;

class PlanDayController extends Controller
{
    public function __construct(private readonly PlanDayService $service) {}

    public function __invoke(PlanDayRequest $request): DayResource
    {
        $user = $request->user();
        $plan = $user->plans()->where('status', 'active')->firstOrFail();

        return new DayResource($this->service->build($user, $plan, $request->planDate()));
    }
}
