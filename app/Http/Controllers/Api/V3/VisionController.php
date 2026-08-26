<?php

namespace App\Http\Controllers\Api\V3;

use App\Ai\Agents\MealPhotoAgent;
use App\Ai\Agents\NutritionLabelAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\AnalyzeImageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Files\Image;
use Throwable;

class VisionController extends Controller
{
    /**
     * Read a nutrition-label photo into per-100 macros (barcode-miss fallback).
     */
    public function label(AnalyzeImageRequest $request): JsonResponse
    {
        try {
            $image = Image::fromUpload($request->file('image'));

            $result = (new NutritionLabelAgent)->prompt('Read the nutrition table in this photo.', [$image]);
        } catch (Throwable $e) {
            return $this->failed('Label', $e);
        }

        return response()->json([
            'data' => [
                'kcal' => $result['kcal'] ?? null,
                'protein_g' => $result['protein_g'] ?? null,
                'carbs_g' => $result['carbs_g'] ?? null,
                'fat_g' => $result['fat_g'] ?? null,
                'serving_size' => $result['serving_size'] ?? null,
                'serving_unit' => $result['serving_unit'] ?? null,
            ],
        ]);
    }

    /**
     * Detect the foods and portions in a meal photo.
     */
    public function meal(AnalyzeImageRequest $request): JsonResponse
    {
        try {
            $image = Image::fromUpload($request->file('image'));

            $language = \Locale::getDisplayLanguage(app()->getLocale(), 'en');

            $result = (new MealPhotoAgent)->prompt("Identify the foods in this meal photo. Name each item in {$language}.", [$image]);
        } catch (Throwable $e) {
            return $this->failed('Meal', $e);
        }

        $items = collect($result['items'] ?? [])->map(fn (array $item): array => [
            'name' => $item['name'] ?? null,
            'portion_g' => $item['portion_g'] ?? null,
            'kcal' => $item['kcal'] ?? null,
            'protein_g' => $item['protein_g'] ?? null,
            'carbs_g' => $item['carbs_g'] ?? null,
            'fat_g' => $item['fat_g'] ?? null,
            'confidence' => $item['confidence'] ?? null,
        ])->values();

        return response()->json(['data' => ['items' => $items]]);
    }

    private function failed(string $subcomponent, Throwable $e): JsonResponse
    {
        report($e);
        Log::warning("[Vision][{$subcomponent}] Analysis failed", ['error' => $e->getMessage()]);

        return response()->json(['message' => 'analysis_failed', 'code' => 'vision_failed'], 503);
    }
}
