<?php

namespace App\Http\Resources\Client;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            "razon_social"  => $this->resource->razon_social,
            "razon_social_comercial"  => $this->resource->razon_social_comercial,
            "phone"  => $this->resource->phone,
            "email"  => $this->resource->email,
            "n_document"  => $this->resource->n_document,
            "birth_date"  => Carbon::parse($this->resource->birth_date)->format("Y-m-d"),
            "ubigeo_region"  => $this->resource->ubigeo_region,
            "ubigeo_provincia"  => $this->resource->ubigeo_provincia,
            "ubigeo_distrito"  => $this->resource->ubigeo_distrito,
            "region"  => $this->resource->region,
            "provincia"  => $this->resource->provincia,
            "distrito"  => $this->resource->distrito,
            "address"  => $this->resource->address,
            "urbanizacion"  => $this->resource->urbanizacion,
            "cod_local"  => $this->resource->cod_local,
        ];
    }
}
