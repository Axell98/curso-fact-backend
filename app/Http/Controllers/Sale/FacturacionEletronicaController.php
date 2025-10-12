<?php

namespace App\Http\Controllers\Sale;

use App\Models\Company;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Nota\ElectronicNote;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use App\Http\Resources\Sale\SaleResource;
use App\Models\Nota\ElectronicNoteDetail;
use App\Http\Controllers\Greenter\GreenterService;
use App\Http\Resources\Nota\ElectronicNoteResource;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FacturacionEletronicaController extends Controller
{
    protected $greenter_service;
    public function __construct(GreenterService $greenter_service) {
        $this->greenter_service = $greenter_service;
    }
    
    public function getCorrelativo($serie) {
        $sale = Sale::where("serie",$serie)->where("correlativo","<>",NULL)
                                            ->where("n_operacion","<>",NULL)
                                            ->orderBy("correlativo","desc")
                                            ->first();
        $correlativo = 1;
        if($sale){
            $correlativo = $sale->correlativo + 1;
        }
        return $correlativo;
    }
    
    public function sunat_seend(Request $request) {

        $sale_id = $request->sale_id;

        $sale = Sale::findOrFail($sale_id);
        
        $company = Company::first();

        $data = $this->setTotales($sale);
        $data = $this->setLegends($data);

        $see = $this->greenter_service->getSee();

        $tipo_operacion = "0101";
        switch ($sale->retencion_igv) {
            case 2://Detracción
                $tipo_operacion = '1001';
                break;
            case 3://Percepción
                $tipo_operacion = '2001';
                break;
            default:
                # code...
                break;
        }
        if($sale->is_exportacion == 1){
            $tipo_operacion = '0200';
        }
        $data["tipo_operacion"] = $tipo_operacion;
        $data['tipo_doc'] = $sale->serie == "F001" ? '01' : '03';
        $data['serie'] = $sale->serie;
        $data['correlativo'] = $this->getCorrelativo($sale->serie);
        $data['tipo_moneda'] = $sale->currency == "S/." ? 'PEN' : 'USD';

        $invoice = $this->greenter_service->getInvoice($data,$company,$sale);
        
        $result = $see->send($invoice);

        $response = $this->greenter_service->sunatResponse($result);

        if(!isset($response["error"])){
            // TODO ESTA OK
            // Crear objeto DOMDocument
            $dom = new \DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($see->getFactory()->getLastXml());
            // Formatear el XML con indentación
            $formattedXml = $dom->saveXML();
            // Generar un nombre único para el archivo
            $file_name = "R-".env("RUC").'-'.$data['correlativo'].'-'.$sale->serie."-".$sale->id.' '.now()->format('Ymd_His'). '.xml';
            // Guardar en el disco storage/app/public/xml
            Storage::disk('public')->put('xml/' . $file_name, $formattedXml);
            // Obtener la ruta pública
            $public_path_xml = Storage::url('xml/' . $file_name);

            $sale->update([
                "correlativo" => $data["correlativo"],
                "n_operacion" => $data["serie"]."-".$data["correlativo"],
                "cdr" => $response['cdrZip'],
                "xml" => $public_path_xml,
            ]);

            return response()->json([
                "sale" => SaleResource::make($sale),
                "response" => $response,
            ]);
        }

        return response()->json([
            "response" => $response,
        ]);
    }

    public function setTotales($sale){

        $data =  [];
        $data['mto_oper_gravadas'] = round($sale->sale_details->where('tip_afe_igv', 10)->sum('subtotal'),2);
        $data['mto_oper_exoneradas'] = round( $sale->sale_details->where('tip_afe_igv', 20)->sum('subtotal'),2);
        $data['mto_oper_inafectas'] = round($sale->sale_details->where('tip_afe_igv', 30)->sum('subtotal'),2);

        $discount_global_anticipo = 0;
        $sales_anticipos = $sale->sales_anticipos ? json_decode($sale->sales_anticipos,true) : [];
        if(sizeof($sales_anticipos) > 0 && $sale->amount_anticipo){//$sale->n_comprobante_anticipo
            $discount_global_anticipo += $sale->amount_anticipo;
        }
        if($sale->discount_global > 0){
            $discount_global_anticipo += $sale->discount_global;
        }
        if($discount_global_anticipo > 0){
            if($data['mto_oper_gravadas'] > 0){
                $data['mto_oper_gravadas'] = $data['mto_oper_gravadas'] - $discount_global_anticipo;
            }
            if($data['mto_oper_exoneradas'] > 0){
                $data['mto_oper_exoneradas'] = $data['mto_oper_exoneradas'] - $discount_global_anticipo;
            }
            if($data['mto_oper_inafectas'] > 0){
                $data['mto_oper_inafectas'] = $data['mto_oper_inafectas'] - $discount_global_anticipo;
            }
        }

        $data['mto_oper_exportacion'] = $sale->sale_details->where('tip_afe_igv', 40)->sum('subtotal');
        $data['mto_oper_gratuitas'] = $sale->sale_details->whereIn('tip_afe_igv', [11,12,13,14,15,16,31,32,33,34,35,36,37])->sum('subtotal');
        $data["mto_base_ivap"] = $sale->sale_details->where('tip_afe_igv', 17)->sum('subtotal');
        $data["mto_ivap"] = $sale->sale_details->where('tip_afe_igv', 17)->sum('igv');
        
        $data['mto_igv'] = round($sale->sale_details->whereIn('tip_afe_igv', [10, 20, 30, 40])->sum('igv'),2) - $sale->igv_discount_general;
        $data['mto_igv_gratuitas'] = $sale->sale_details->whereIn('tip_afe_igv', [11,12,13,14,15,16,31,32,33,34,35,36,37])->sum('igv');
        $data['icbper'] = $sale->sale_details->sum('icbper');
        $data['isc'] = $sale->sale_details->sum('isc');
        $data['mto_oper_isc'] = round($sale->sale_details->where('isc',">",0)->sum('subtotal'),2);

        $data['total_impuestos'] = $data['mto_igv'] + $data['icbper'] + $data["mto_ivap"] + $data['isc'];

        $data['valor_venta'] = round($sale->sale_details->whereIn('tip_afe_igv', [10, 20, 30, 40, 17])->sum('subtotal'),2) - $sale->discount_global; //- $discount_global_anticipo;
        $data['sub_total'] = $data['valor_venta'] + $data['total_impuestos'];
        
        $data['mto_imp_venta'] = floor($data['sub_total'] * 10) / 10;
        $data['redondeo'] = $data['mto_imp_venta'] - $data['sub_total'];
        
        $sales_anticipos = $sale->sales_anticipos ? json_decode($sale->sales_anticipos,true) : [];
        if(sizeof($sales_anticipos) > 0 && $sale->amount_anticipo){//$sale->n_comprobante_anticipo
            $data['sub_total'] = $data['sub_total'] + round($sale->amount_anticipo*0.18,2);
            $data['mto_imp_venta'] = $data['sub_total'] - $sale->amount_anticipo;
        }
        error_log(json_encode($data));
        return $data;
    }

    public function setLegends($data){
        $formatter = new NumeroALetras();
        $data['legends'] = [
            [
                'code' => '1000',
                'value' => $formatter->toInvoice($data['mto_imp_venta'], 2, 'SOLES')
            ]
        ];
        return $data;
    }

    public function getcorrelativoNote() {
        $electronic_note = ElectronicNote::where("correlativo","<>",NULL)
                                            ->where("n_operacion","<>",NULL)
                                            ->orderBy("correlativo","desc")
                                            ->first();
        $correlativo = 1;
        if($electronic_note){
            $correlativo = $electronic_note->correlativo + 1;
        }
        return $correlativo;
    }

    public function sunat_nota_seend(Request $request) {

        $sale_id = $request->sale_id;
        $sale_details = $request->sale_details;
        $sale = Sale::findOrFail($sale_id);

        try {
            DB::beginTransaction();

            $note_electronic = ElectronicNote::create([
                "serie" => $request->serie,
                "user_id" => auth('api')->user()->id,
                "n_comprobante" => $sale->n_operacion,
                "client_id" => $request->client_id,
                "type_client" => $request->type_client,
                "subtotal" => $request->subtotal,
                "discount" => $request->discount,
                "total" => $request->total,
                "igv" => $request->igv,
                
                // 
                "state_sale" => $request->state_sale,
                "state_payment" => $request->state_payment,
                "type_payment" => $request->type_payment,
                "debt" => $request->debt,
                "paid_out" => $request->paid_out,
                "description" => $request->description,
                // "amount_anticipo" => $request->amount_anticipo ?? 0,
                // "sales_anticipos" => $request->sales_anticipos ? json_encode($request->sales_anticipos) : null,
                // 
                "retencion_igv" => $request->retencion_igv,
                "igv_discount_general" => $request->igv_discount_general,
                "discount_global" => $request->discount_global,
                "is_exportacion" => $request->is_exportacion,
                "currency" => $request->currency,
                // 
                "doc_nota" => $request->doc_nota,
                "type_nota" => $request->type_nota,
                "description_motivo" => $request->description_motivo,
            ]);

            foreach ($sale_details as $key => $sale_detail) {
                $electronic_note_id = ElectronicNoteDetail::create([
                    "electronic_note_id" => $note_electronic->id,
                    "product_id" => $sale_detail["product"]["id"],
                    "product_categorie_id" => $sale_detail["product"]["categorie_id"],
                    "unidad_medida" => $sale_detail["unidad_medida"],
                    "quantity" => $sale_detail["quantity"],//LA CANTIDAD SOLICITADA
                    "price_final" => $sale_detail["price_final"],
                    "price_base" => $sale_detail["price_base"],
                    "discount" => $sale_detail["discount"],
                    "subtotal" => $sale_detail["subtotal"],
                    "igv" => $sale_detail["igv"],
                    
                    "tip_afe_igv" => $sale_detail["tip_afe_igv"],
                    "per_icbper" => $sale_detail["per_icbper"],
                    "icbper" => $sale_detail["icbper"],
                    "percentage_isc" => $sale_detail["percentage_isc"],
                    "isc" => $sale_detail["isc"],
                ]);
            }

            // 

            $company = Company::first();

            $data = $this->setTotales($note_electronic);
            $data = $this->setLegends($data);

            $see =  $this->greenter_service->getSee();

            $data['tipo_doc'] = $request->doc_nota;// 07: Nota de Crédito / 08: Nota de Débito /
            $data['serie'] = $request->serie;
            $data['correlativo'] = $this->getcorrelativoNote();
            $data['tipo_moneda'] = $request->currency == 'S/.' ? "PEN" : "USD";
            $data['tipo_doc_afect'] = $sale->serie == 'F001' ? '01' : '03';
            $data['num_doc_afect'] = $sale->n_operacion;//F001-50
            $data['cod_motivo'] = $request->type_nota;
            $data['des_motivo'] = $request->description_motivo;

            $nota_electronic = $this->greenter_service->getNota($data,$company,$note_electronic);

            $result = $see->send($nota_electronic);

            $response = $this->greenter_service->sunatResponse($result);

            if(!isset($response['error'])){

                // Crear objeto DOMDocument
                $dom = new \DOMDocument('1.0');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($see->getFactory()->getLastXml());
                // Formatear el XML con indentación
                $formattedXml = $dom->saveXML();
                // Generar un nombre único para el archivo
                $file_name = "FF-".env("RUC").'-'.$data['correlativo'].'-'.$note_electronic->serie."-".$note_electronic->id.' '.now()->format('Ymd_His'). '.xml';
                // Guardar en el disco storage/app/public/xml
                Storage::disk('public')->put('xml/' . $file_name, $formattedXml);
                // Obtener la ruta pública
                $public_path_xml = Storage::url('xml/' . $file_name);

                $note_electronic->update([
                    "correlativo" => $data['correlativo'],
                    "n_operacion" => $note_electronic->serie."-".$data['correlativo'],
                    "cdr" => $response['cdrZip'],
                    "xml" => $public_path_xml,
                ]);
                DB::commit();
                return response()->json([
                    "note_electronic" => ElectronicNoteResource::make($note_electronic),
                    "message" => "LA NOTA ELECTRONICA SE REGISTRO Y EMITIO CORRECTAMENTE",
                    "response" => $response,
                ]);
            }
            return response()->json($response);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new HttpException(500,$th->getMessage());
            //throw $th;
        }
    }

    public function pdf($n_operacion) {
        $electronic_note = ElectronicNote::where("n_operacion",$n_operacion)->first();
        if(!$electronic_note){
            return abort(404);
        }
        $pdf = Pdf::loadView('pdf_electronic_note', compact('electronic_note'));
        return $pdf->stream('nota_electronica_'.$n_operacion.'.pdf');
    }
}
