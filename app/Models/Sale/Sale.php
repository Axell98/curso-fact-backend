<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "serie",
        "correlativo",
        "n_operacion",
        "user_id",
        "client_id",
        "type_client",
        "subtotal",
        "total",
        "igv",
        "state_sale",
        "state_payment",
        "type_payment",
        "debt",
        "paid_out",

        "description",
        "discount",
        "retencion_igv",
        "discount_global",
        "igv_discount_general",
        "n_comprobante_anticipo",
        "amount_anticipo",
        "cdr",
        "xml",
        "is_exportacion",
        "currency",
        // "sales_anticipos",
    ];

    public function setCreatedAtAttribute($value)
    {
    	date_default_timezone_set('America/Lima');
        $this->attributes["created_at"]= Carbon::now();
    }

    public function setUpdatedAtAttribute($value)
    {
    	date_default_timezone_set("America/Lima");
        $this->attributes["updated_at"]= Carbon::now();
    }

    public function user(){
        return $this->belongsTo(User::class,"user_id");
    }

    public function client(){
        return $this->belongsTo(Client::class,"client_id");
    }

    public function sale_details() {
        return $this->hasMany(SaleDetail::class,"sale_id");
    }

    public function sale_payments() {
        return $this->hasMany(SalePayment::class,"sale_id");
    }

    public function getFirstPaymentAttribute() {
        return $this->sale_payments->first() ? $this->sale_payments->first() : NULL;
    }

    public function scopeFilterMultiple($query,$search_product,$categorie_id,$search,$search_client,
    $state_sale,$type_payment,$start_date,$end_date){

        if($search_product){
            $query->whereHas("sale_details",function($q) use($search_product){
                $q->whereHas("product",function($suq) use($search_product){
                    $suq->where(DB::raw("CONCAT(products.title,' ',products.sku)"),"like","%".$search_product."%");
                });
            });
        }

        if($categorie_id){
            $query->whereHas("sale_details",function($q) use($categorie_id){
                $q->where("product_categorie_id",$categorie_id);
            });
        }

        if($search){
            $query->where("id",$search);
        }

        if($search_client){
            $query->whereHas("client",function($q) use($search_client){
                $q->where(DB::raw("CONCAT(clients.full_name,' ',IFNULL(clients.phone,''),' ',
                    IFNULL(clients.email,''),' ',IFNULL(clients.n_document,''))"),"like","%".$search_client."%");
            });
        }

        if($state_sale){
            $query->where("state_sale",$state_sale);
        }
        if($type_payment){
            $query->where("type_payment",$type_payment);
        }

        if($start_date && $end_date){
            $query->whereBetween("created_at",[
                Carbon::parse($start_date)->format("Y-m-d")." 00:00:00",
                Carbon::parse($end_date)->format("Y-m-d")." 23:59:59",
            ]);
        }
        return $query;
    }
}
