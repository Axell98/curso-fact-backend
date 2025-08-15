<?php

namespace App\Http\Resources\Sale;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $igv_general = $this->resource->igv + $this->resource->igv_discount_general;
        
        $isc_total = $this->resource->sale_details->sum("isc");
        $icbper_total = $this->resource->sale_details->sum("icbper");
        $total_percepcion = 0;
        if($this->resource->retencion_igv == 3){
            $total_percepcion = round(($this->resource->total * 0.04),5);
        }
        $total_retencion = 0;
        if($this->resource->retencion_igv == 1){
            $total_retencion = round(($this->resource->total * 0.03),5);
        }
        $total_detracion = 0;
        if($this->resource->retencion_igv == 2){
            $total_detracion = round(($this->resource->total * 0.04),5);
        }
        $total_general = ($this->resource->total + $isc_total + $icbper_total + $total_percepcion) - 
                        ($this->resource->discount_global + $this->resource->igv_discount_general + $total_retencion + $total_detracion);

        //     return Number(((sale_total.value + icbper_total.value + isc_total.value + total_percepcion.value) - 
        // (discount_global.value + igv_discount_global.value + total_retencion.value + total_detracion.value)).toFixed(5));
        return [
            "id" => $this->resource->id,
            "n_transaction" => str_pad($this->resource->id, 8, "0", STR_PAD_LEFT),
            "serie" => $this->resource->serie,
            "correlativo" => $this->resource->correlativo,
            "n_operacion" => $this->resource->n_operacion,
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
            "type_client" => $this->resource->type_client,
            "subtotal" => $this->resource->subtotal,
            "total" => $this->resource->total,
            "igv" => $this->resource->igv,
            "state_sale" => $this->resource->state_sale,
            "state_payment" => $this->resource->state_payment,
            "type_payment" => $this->resource->type_payment,
            "debt" => $this->resource->debt,
            "paid_out" => $this->resource->paid_out,

            "igv_general" => round($igv_general,2),
            "total_general" =>  round($total_general,2),

            "description" => $this->resource->description,
            "discount" => $this->resource->discount,
            "retencion_igv" => $this->resource->retencion_igv,
            "discount_global" => $this->resource->discount_global,
            "igv_discount_general" => $this->resource->igv_discount_general,
            "n_comprobante_anticipo" => $this->resource->n_comprobante_anticipo,
            // "sales_anticipos" => $this->resource->sales_anticipos ? json_decode($this->resource->sales_anticipos,true) : null,
            "amount_anticipo" => $this->resource->amount_anticipo,
            "cdr" => $this->resource->cdr ? env("APP_URL_XML_CDR").$this->resource->cdr : NULL,
            "xml" => $this->resource->xml ? env("APP_URL_XML_CDR").$this->resource->xml : NULL,
            "is_exportacion" => $this->resource->is_exportacion,
            "currency" => $this->resource->currency,
            "created_at" => $this->resource->created_at->format("Y-m-d h:i A"),
            "created_at_format" => $this->resource->created_at->format("Y-m-d"),

            "sale_details" => $this->resource->sale_details->map(function($sale_detail) {
                return SaleDetailResource::make($sale_detail);
            }),
            "payments" => $this->resource->sale_payments->map(function($sale_payment) {
                return [
                    "id" => $sale_payment->id,
                    "method_payment"  => $sale_payment->method_payment,
                    "amount"  => $sale_payment->amount,
                    "date_payment"  => $sale_payment->date_payment ? Carbon::parse($sale_payment->date_payment)->format("Y-m-d") : NULL,
                ];
            }),
            "notas" => [],
        ];
    }
}
