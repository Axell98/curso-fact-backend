<?php

namespace App\Models\Guia;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaRemision extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "serie",
        "correlativo",
        "n_operacion",
        "user_id",
        "client_id",
        "type_client",
        "total",
        "quantity_total",
        "motivo_translado",
        "type_transport",
        "punto_partida",
        "punto_llegada",
        "transporte_datos",
        "conductor_datos",
        "description",
        "cdr",
        "xml",
        "num_dam"
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

    public function user() {
        return $this->belongsTo(User::class,"user_id");
    }

    public function client() {
        return $this->belongsTo(Client::class,"client_id");
    }

    public function details() {
        return $this->hasMany(GuiaRemisionDetail::class,"guia_remision_id");
    }

    public function scopeFilterMultiple($query,$search_product,$categorie_id,$search,$search_client,
    $type_transport,$motivo_translado,$start_date,$end_date){

        if($search_product){
            $query->whereHas("details",function($q) use($search_product){
                $q->whereHas("product",function($suq) use($search_product){
                    $suq->where(DB::raw("CONCAT(products.title,' ',products.sku)"),"like","%".$search_product."%");
                });
            });
        }

        if($categorie_id){
            $query->whereHas("details",function($q) use($categorie_id){
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

        if($type_transport){
            $query->where("type_transport",$type_transport);
        }
        if($motivo_translado){
            $query->where("motivo_translado",$motivo_translado);
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
