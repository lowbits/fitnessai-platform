<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Observers\RecipeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy([RecipeObserver::class])]
class Recipe extends Model
{
    use HasFactory, HasSlug, HasTranslations, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'instructions' => 'array',
            'tags' => 'array',
            'allergens' => 'array',
            'meal_types' => 'array',
            'is_verified' => 'boolean',
            'needs_translation' => 'boolean',
        ];
    }

    public function toSearchableArray(): array
    {
        $searchText = collect([
            $this->name,
            $this->description,
            $this->primary_protein,
            $this->cuisine,
            $this->difficulty,
        ])->filter()->implode(' | ');

        $allNames = collect([$this->name]);

        $this->translations->each(function (RecipeTranslation $translation) use ($allNames) {
            $allNames->push($translation->name);

            if ($translation->aliases) {
                $allNames->push(...$translation->aliases);
            }
        });

        $translations = $this->translations
            ->mapWithKeys(fn (RecipeTranslation $t) => [$t->locale => $t->name])
            ->put('en', $this->name)
            ->sortKeys()
            ->all();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'translations' => $translations,
            'all_names' => $allNames->unique()->values()->toArray(),
            'search_text' => $searchText,
            'primary_protein' => $this->primary_protein,
            'cuisine' => $this->cuisine,
            'format' => $this->format,
            'hero_veg' => $this->hero_veg,
            'source_locale' => $this->source_locale,
            'difficulty' => $this->difficulty,
            'tags' => $this->tags ?? [],
            'allergens' => $this->allergens ?? [],
            'meal_types' => $this->meal_types ?? [],
            'calories' => $this->calories,
            'protein_g' => (int) $this->protein_g,
            'total_time_minutes' => ($this->prep_time_minutes ?? 0) + ($this->cook_time_minutes ?? 0),
            'ingredient_names' => collect($this->ingredients ?? [])
                ->pluck('name')
                ->map(fn ($name) => strtolower(trim($name)))
                ->values()
                ->toArray(),
            'image_full' => $this->image_full,
            'image_isolated' => $this->image_isolated,
            'is_verified' => $this->is_verified,
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(RecipeTranslation::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function localizedSlug(?string $locale = null): string
    {
        return $this->localized('slug', $locale) ?? $this->slug;
    }

    public function localizedIngredients(?string $locale = null): ?array
    {
        return $this->localized('ingredients', $locale);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
