<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'description' => $this->description,
            'quantity' => $this->quantity ?? 0,
            'in_stock' => ($this->quantity ?? 0) > 0,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
        ];
    }
}
