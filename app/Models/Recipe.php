<?php

namespace App\Models;

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
    use HasFactory, HasSlug, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'instructions' => 'array',
            'tags' => 'array',
            'allergens' => 'array',
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
            'difficulty' => $this->difficulty,
            'tags' => $this->tags ?? [],
            'allergens' => $this->allergens ?? [],
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

    public function translation(?string $locale = null): ?RecipeTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale);
    }

    public function localizedSlug(?string $locale = null): string
    {
        return $this->translation($locale)?->slug ?? $this->slug;
    }

    public function localizedName(?string $locale = null): string
    {
        return $this->translation($locale)?->name ?? $this->name;
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        return $this->translation($locale)?->description ?? $this->description;
    }

    public function localizedInstructions(?string $locale = null): ?array
    {
        return $this->translation($locale)?->instructions ?? $this->instructions;
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
