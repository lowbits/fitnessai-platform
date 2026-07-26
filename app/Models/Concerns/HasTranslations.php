<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Shared translation helpers for models with a `translations` HasMany relationship.
 *
 * The translation model must have at least: locale, name.
 * Use `localized()` for any field, or the typed shortcuts for common ones.
 *
 * @mixin Model
 */
trait HasTranslations
{
    public function translation(?string $locale = null): ?Model
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale);
    }

    /**
     * Get a translated field value, falling back to the original.
     */
    public function localized(string $field, ?string $locale = null): mixed
    {
        return $this->translation($locale)?->{$field} ?? $this->{$field};
    }

    public function localizedName(?string $locale = null): string
    {
        return $this->localized('name', $locale);
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        return $this->localized('description', $locale);
    }

    public function localizedInstructions(?string $locale = null): ?array
    {
        return $this->localized('instructions', $locale);
    }
}
