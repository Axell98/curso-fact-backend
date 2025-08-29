<?php

namespace App\Models\Nota;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElectronicNote extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "serie",
        "correlativo",
        "n_operacion",
        "n_comprobante",
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
        "cdr",
        "xml",
        "is_exportacion",
        "currency",
        "amount_anticipo",
        "sales_anticipos",
        // 
        "doc_nota",
        "type_nota",
        "description_motivo"
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

    public function details() {
        return $this->hasMany(ElectronicNoteDetail::class,"electronic_note_id");
    }

    public function sale_details() {
        return $this->hasMany(ElectronicNoteDetail::class,"electronic_note_id");
    }
}
