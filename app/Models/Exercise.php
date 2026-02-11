<?php

namespace App\Models;

use App\Observers\ExerciseObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy([ExerciseObserver::class])]
class Exercise extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'primary_muscles' => 'array',
            'secondary_muscles' => 'array',
            'equipment' => 'array',
            'training_places' => 'array',
            'instructions' => 'array',
            'event_tags' => 'array',
            'requires_spotter' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function toSearchableArray(): array
    {
        $searchText = collect([
            $this->name,
            $this->description,
            $this->type,
            $this->movement_pattern,
            $this->mechanic,
            $this->body_region,
        ])->filter()->implode(' | ');

        $allNames = collect([$this->name]);

        $this->translations->each(function (ExerciseTranslation $translation) use ($allNames) {
            $allNames->push($translation->name);

            if ($translation->aliases) {
                $allNames->push(...$translation->aliases);
            }
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'all_names' => $allNames->unique()->values()->toArray(),
            'search_text' => $searchText,
            'type' => $this->type,
            'movement_pattern' => $this->movement_pattern,
            'mechanic' => $this->mechanic,
            'body_region' => $this->body_region,
            'difficulty' => $this->difficulty,
            'primary_muscles' => $this->primary_muscles ?? [],
            'secondary_muscles' => $this->secondary_muscles ?? [],
            'equipment' => $this->equipment ?? [],
            'training_places' => $this->training_places ?? [],
            'event_tags' => $this->event_tags ?? [],
            'requires_spotter' => $this->requires_spotter,
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
        return $this->hasMany(ExerciseTranslation::class);
    }

    public function workoutPlanExercises(): HasMany
    {
        return $this->hasMany(WorkoutPlanExercise::class);
    }

    public function translation(?string $locale = null): ?ExerciseTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale);
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

    public function localizedFormCues(?string $locale = null): ?string
    {
        return $this->translation($locale)?->form_cues ?? $this->form_cues;
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForTrainingPlace($query, string $place)
    {
        return $query->whereJsonContains('training_places', $place);
    }

    public function scopeForMuscle($query, string $muscle)
    {
        return $query->where(function ($q) use ($muscle) {
            $q->whereJsonContains('primary_muscles', $muscle)
                ->orWhereJsonContains('secondary_muscles', $muscle);
        });
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->whereJsonContains('event_tags', $event);
    }

    public function scopeWithEquipment($query, string $equipment)
    {
        return $query->whereJsonContains('equipment', $equipment);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function allMuscles(): array
    {
        return array_unique(array_merge(
            $this->primary_muscles ?? [],
            $this->secondary_muscles ?? [],
        ));
    }

    public function isAvailableAt(string $place): bool
    {
        return in_array($place, $this->training_places ?? [])
            || in_array('anywhere', $this->training_places ?? []);
    }
}
