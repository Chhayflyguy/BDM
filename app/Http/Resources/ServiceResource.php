<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'duration_minutes' => $this->duration_minutes,
            'description' => $this->description,
            'image_url' => $this->image ? Storage::url($this->image) : null,
        ];
    }
}