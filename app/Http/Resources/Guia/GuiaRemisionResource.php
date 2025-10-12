<?php

namespace App\Http\Resources\Guia;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuiaRemisionResource extends JsonResource
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
            "user_id"  => $this->resource->user_id,
            "user" => [
                "full_name" => $this->resource->user->name.' '.$this->resource->user->surname,
            ],
            "client_id"  => $this->resource->client_id,
            "client" => [
                "id" => $this->resource->client->id,
                "full_name" => $this->resource->client->full_name,
                "n_document" => $this->resource->client->n_document,
                "type_client" => $this->resource->client->type_client,
            ],
            "correlativo_id" => str_pad($this->resource->id, 8, "0", STR_PAD_LEFT),
            "correlativo" => $this->resource->correlativo,
            "n_operacion" => $this->resource->n_operacion,
            "serie" => $this->resource->serie,
            // 
            "cdr" => $this->resource->cdr ? env("APP_URL_XML_CDR").$this->resource->cdr : null,
            "xml" => $this->resource->cdr ? env("APP_URL_XML_CDR").$this->resource->xml : null,
            // 
            "type_client"  => $this->resource->type_client,
            "total"  => (float) $this->resource->total,
            "quantity_total"  => (int) $this->resource->quantity_total,
            // 
            "motivo_translado"  => $this->resource->motivo_translado,
            "type_transport"  => $this->resource->type_transport,
            // 
            "punto_partida"  => json_decode($this->resource->punto_partida,true),
            "punto_llegada"  => json_decode($this->resource->punto_llegada,true),
            "transporte_datos"  => json_decode($this->resource->transporte_datos,true),
            "conductor_datos" => json_decode($this->resource->conductor_datos,true),
            // 
            "description"  => $this->resource->description,
            "created_at" => $this->resource->created_at->format("Y-m-d h:i A"),
            "created_at_format" => $this->resource->created_at->format("Y-m-d"),
            "details" => $this->resource->details->map(function($detail) {
                return GuiaDetailRemisionResource::make($detail);
            }),
        ];
    }
}
