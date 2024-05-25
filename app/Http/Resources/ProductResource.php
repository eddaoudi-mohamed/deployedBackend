<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category->name,
            'price' => $this->price,
            'quantityPreUnit' => $this->quantityPreUnit,
            'unitsInStock' => $this->unitsInStock,
            'unitsOnOrder' => $this->unitsOnOrder,
            'status' => $this->status,
            "image" => asset(Storage::url($this->image)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
