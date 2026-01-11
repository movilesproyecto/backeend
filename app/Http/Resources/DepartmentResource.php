<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'address' => $this->address ?? '',
            'bedrooms' => $this->bedrooms ?? 1,
            'pricePerNight' => $this->price_per_night ?? $this->pricePerNight ?? 50,
            'rating' => $this->rating_avg ?? $this->rating ?? 4.0,
            'description' => $this->description ?? '',
            'amenities' => is_string($this->amenities) ? json_decode($this->amenities, true) : ($this->amenities ?? []),
            'images' => is_string($this->images) ? json_decode($this->images, true) : ($this->images ?? []),
            'published' => (bool)($this->published ?? true),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
