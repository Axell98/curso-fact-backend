<?php

namespace App\Http\Resources\Nota;

use Illuminate\Http\Request;
use App\Http\Resources\Sale\SaleDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ElectronicNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $igv_general = $this->resource->igv + $this->resource->igv_discount_general;

        $discount_global_general = 0;

        if($this->resource->discount_global > 0){
            $discount_global_general += $this->resource->discount_global;
        }

        if($this->resource->amount_anticipo > 0){
            $discount_global_general += $this->resource->amount_anticipo;
        }

        $isc_total = $this->resource->sale_details->sum("isc");
        $icbper_total = $this->resource->sale_details->sum("icbper");
        $total_percepcion = 0;
        $TOTAL_CAL = ($this->resource->total + $isc_total + $icbper_total) - ($discount_global_general + $this->resource->igv_discount_general);

        if($this->resource->retencion_igv == 3){
            $total_percepcion = round(($TOTAL_CAL * 0.04),5);
        }
        $total_retencion = 0;
        if($this->resource->retencion_igv == 1){
            $total_retencion = round(($TOTAL_CAL * 0.03),5);
        }
        $total_detracion = 0;
        if($this->resource->retencion_igv == 2){
            $total_detracion = round(($TOTAL_CAL * 0.04),5);
        }
        $total_general = ($this->resource->total + $isc_total + $icbper_total + $total_percepcion) - 
                        ($discount_global_general + $this->resource->igv_discount_general + $total_retencion + $total_detracion);

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
            // 
            "serie" => $this->resource->serie,
            "retencion_igv" => (float) $this->resource->retencion_igv,
            "discount_global" => (float) $this->resource->discount_global,
            // 
            "doc_nota" => $this->resource->doc_nota,
            "n_comprobante" => $this->resource->n_comprobante,
            "type_nota" => $this->resource->type_nota,
            "description_motivo" => $this->resource->description_motivo,
            // 
            "amount_anticipo" => $this->resource->amount_anticipo,
            "sales_anticipos" => $this->resource->sales_anticipos ? json_decode($this->resource->sales_anticipos,true) : null,
            "cdr" => env("APP_URL_XML_CDR").$this->resource->cdr,
            "xml" => env("APP_URL_XML_CDR").$this->resource->xml,
            "url" => env("APP_URL")."api/electronic-note-pdf/".$this->resource->n_operacion,
            // 
            "correlativo" => $this->resource->correlativo,
            "n_operacion" => $this->resource->n_operacion,
            "type_client"  => $this->resource->type_client,
            "subtotal"  => (float) $this->resource->subtotal,
            "discount"  => (float) $this->resource->discount,
            // 
            "total_general"  => round($total_general,2),
            "igv_general"  => round($igv_general,2),
            // 
            "total"  => (float) $this->resource->total,
            "igv"  => (float) $this->resource->igv,
            "is_exportacion"  => $this->resource->is_exportacion,
            "currency"  => $this->resource->currency,

            "state_sale"  => $this->resource->state_sale,
            "state_payment"  => $this->resource->state_payment,
            "state_entrega" => $this->resource->state_entrega,
            "type_payment" => $this->resource->type_payment,
            "debt"  => (float) $this->resource->debt,
            "paid_out"  => (float) $this->resource->paid_out,
            "description"  => $this->resource->description,
            "created_at" => $this->resource->created_at->format("Y-m-d h:i A"),
            "created_at_format" => $this->resource->created_at->format("Y-m-d"),
            "details" => $this->resource->details->map(function($sale_detail) {
                return SaleDetailResource::make($sale_detail);
            }),
        ];
    }
}
