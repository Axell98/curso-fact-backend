<?php

namespace App\Models\Product;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "title",//titulo
        "sku",
        "imagen",
        "categorie_id",
        "price_general",
        "price_company",
        "description",
        "is_discount",
        "max_discount",
        "disponiblidad",
        "state",
        "unidad_medida",
        "stock",
        "include_igv",
        
        "is_icbper",
        "is_ivap",
        "percentage_isc",
        "is_especial_nota",
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

    public function categorie() {
        return $this->belongsTo(Categorie::class,"categorie_id");
    }    

    public function scopeFilterMultiple($query,$search,$categorie_id,$status,$unidad_medida){

        if($search){
            $query->where(DB::raw("CONCAT(products.title,' ',products.sku)"),"like","%".$search."%");
        }
        if($categorie_id){
            $query->where("categorie_id",$categorie_id);
        }
        if($status){
            $query->where("state",$status);
        }
        if($unidad_medida){
            $query->where("unidad_medida",$unidad_medida);
        }
        return $query;  
    }
}
