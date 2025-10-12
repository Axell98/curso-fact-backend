<?php

namespace App\Http\Resources\Guia;

use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GuiaDetailRemisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "product_id" => $this->resource->product_id,
            "product" => ProductResource::make($this->resource->product),
            "product_categorie_id" => $this->resource->product_categorie_id,
            "product_categorie" => [
                "title" => $this->resource->categorie->title,
            ],
            "unidad_medida" => $this->resource->product->unidad_medida,
            "quantity" => (int) $this->resource->quantity,
            "peso" => (float) $this->resource->peso,
            "total" => (float) $this->resource->total,
            "description" => $this->resource->description,
        ];
    }
}
