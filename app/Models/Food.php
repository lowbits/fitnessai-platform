<?php

namespace App\Models;

use App\Enums\FoodSource;
use Database\Factories\FoodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Food extends Model
{
    /** @use HasFactory<FoodFactory> */
    use HasFactory;

    use Searchable;

    protected $table = 'foods';

    protected $fillable = [
        'source', 'user_id', 'barcode', 'name', 'brand',
        'image_url', 'image_thumb_url',
        'kcal', 'protein_g', 'carbs_g', 'fat_g',
        'fiber_g', 'sugar_g', 'sat_fat_g', 'salt_g',
        'serving_size', 'serving_unit', 'verified',
    ];

    protected function casts(): array
    {
        return [
            'source' => FoodSource::class,
            'kcal' => 'float',
            'protein_g' => 'float',
            'carbs_g' => 'float',
            'fat_g' => 'float',
            'fiber_g' => 'float',
            'sugar_g' => 'float',
            'sat_fat_g' => 'float',
            'salt_g' => 'float',
            'serving_size' => 'float',
            'verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function searchableAs(): string
    {
        return config('services.openfoodfacts.index', 'products');
    }

    /**
     * Get the value used to index the model.
     */
    public function getScoutKey(): string
    {
        return $this->barcode ?: 'food-'.$this->getKey();
    }

    /**
     * Get the key name used to index the model.
     */
    public function getScoutKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->getScoutKey(),
            'source' => $this->source->value,
            'user_id' => $this->user_id,
            'barcode' => $this->barcode,
            'product_name' => $this->name,
            'brands' => $this->brand,
            'image_url' => $this->image_url,
            'image_thumb_url' => $this->image_thumb_url,
            'energy_kcal_100g' => $this->kcal,
            'proteins_100g' => $this->protein_g,
            'carbohydrates_100g' => $this->carbs_g,
            'fat_100g' => $this->fat_g,
        ];
    }
}
