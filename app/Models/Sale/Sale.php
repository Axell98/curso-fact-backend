<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client\Client;
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
}
