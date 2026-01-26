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
        // Obtener las imágenes: pueden venir del JSON column o de la relación
        $images = [];

        // Primero intenta obtener desde el column JSON (URLs guardadas directamente)
        if (isset($this->images) && is_string($this->images)) {
            $imageUrls = json_decode($this->images, true) ?? [];
            $images = array_map(function($url, $index) {
                return [
                    'id' => $index,
                    'url' => $url,
                    'fileName' => basename($url),
                ];
            }, $imageUrls, array_keys($imageUrls));
        }
        // Si no hay JSON, intenta obtener de la relación (tabla images) o array de URLs
        elseif ($this->images && is_iterable($this->images) && count($this->images) > 0) {
            $firstImage = $this->images[0] ?? null;
            if (is_object($firstImage)) {
                // Es una colección de modelos Image
                $images = collect($this->images)->map(function($image) {
                    return [
                        'id' => $image->id ?? 0,
                        'url' => url('/storage/' . $image->file_path),
                        'fileName' => $image->file_name,
                    ];
                })->toArray();
            } elseif (is_string($firstImage)) {
                // Es un array de URLs
                $images = collect($this->images)->map(function($url, $index) {
                    return [
                        'id' => $index,
                        'url' => $url,
                        'fileName' => basename(parse_url($url, PHP_URL_PATH) ?: $url),
                    ];
                })->toArray();
            }
        }

        // Convertir imagen binaria (bytea) a base64 si existe
        $imageBinary = null;
        if ($this->images_binary) {
            try {
                $imageData = $this->images_binary;
                // Si es un resource (stream de PostgreSQL), convertir a string
                if (is_resource($imageData)) {
                    $imageData = stream_get_contents($imageData);
                }
                // El seeder guarda directamente base64
                if ($imageData) {
                    $imageBinary = 'data:image/jpeg;base64,' . $imageData;
                }
            } catch (\Exception $e) {
                // Si hay error, dejar imageBinary como null
            }
        }

        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'address' => $this->address ?? '',
            'bedrooms' => $this->bedrooms ?? 1,
            'pricePerNight' => $this->price_per_night ?? $this->pricePerNight ?? 50,
            'rating' => $this->rating_avg ?? $this->rating ?? 4.0,
            'description' => $this->description ?? '',
            'amenities' => is_string($this->amenities) ? json_decode($this->amenities, true) : ($this->amenities ?? []),
            'images' => $images,
            'imageBinary' => $imageBinary,
            'favorited_by' => $this->whenLoaded('favoritedBy', function () {
                return $this->favoritedBy->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                });
            }),
            'published' => (bool)($this->published ?? true),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
