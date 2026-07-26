<?php

namespace App\Http\Resources\Api\V3;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->avatar,
            'locale' => $this->locale,
            'email_verified' => $this->email_verified_at !== null,
            'trial_ends_at' => $this->trial_ends_at,
            'next_generation_at' => $this->next_generation_at,
            'profile' => new UserProfileResource($this->whenLoaded('profile')),
        ];
    }
}
