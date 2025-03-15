<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => ucfirst($this->name),
            'description' => $this->description,
            'price' => '$'. number_format($this->price, 2, '.', ','),
            'stock' => $this->stock,
            // 'exist' => $this->stock > 0 ? true : false,
            // 'pedir_mas' => $this->stock < 100 ? true : false
        ];
    }
}
