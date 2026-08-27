<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\ConsentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\StoreConsentRequest;
use App\Models\UserConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConsentController extends Controller
{
    public function current(): JsonResponse
    {
        $locale = app()->getLocale();

        return response()->json([
            'version' => config('consent.current_version'),
            'providers' => config('consent.providers'),
            'copy' => config("consent.copy.{$locale}") ?? config('consent.copy.en'),
        ]);
    }

    public function store(StoreConsentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $consent = $request->user()->consents()->create([
            'consent_type' => $validated['type'],
            'version' => $validated['version'],
            'granted_at' => now(),
            'source' => $validated['source'],
            'locale' => $validated['locale'],
        ]);

        return response()->json(['version' => $consent->version], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(ConsentType::class)],
        ]);

        UserConsent::activeFor($request->user(), ConsentType::from($validated['type']))?->revoke();

        return response()->json(['version' => null]);
    }
}
