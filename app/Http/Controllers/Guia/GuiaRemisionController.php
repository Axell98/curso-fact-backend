<?php

namespace App\Http\Controllers\Guia;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Models\Product\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Guia\GuiaRemision;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Guia\GuiaRemisionDetail;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Client\ClientCollection;
use App\Http\Resources\Guia\GuiaRemisionResource;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Controllers\Greenter\GreenterService;
use App\Http\Resources\Guia\GuiaRemisionCollection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GuiaRemisionController extends Controller
{
    protected $greenter_service;
    public function __construct(GreenterService $greenter_service) {
        $this->greenter_service = $greenter_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search_product = $request->search_product;
        $categorie_id = $request->categorie_id;
        $search = $request->search;
        $search_client = $request->search_client;
        $type_transport = $request->type_transport;
        $motivo_translado = $request->motivo_translado;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $guia_remisions = GuiaRemision::filterMultiple($search_product,$categorie_id,$search,$search_client,$type_transport,$motivo_translado,$start_date,$end_date)
                        ->orderBy("id","desc")
                        ->paginate(25);

        return response()->json([
            "total" => $guia_remisions->total(),
            "paginate" => 25,
            "guia_remisions" => GuiaRemisionCollection::make($guia_remisions),
        ]);
    }

    public function config() {
        
        $today = today();
        $clients = Client::where("state",1)->orderBy("full_name","asc")->get();
        $products = Product::where("state",1)->where("is_especial_nota",0)->orderBy("title","asc")->get();

        $n_transaction = 1000;
        $guia = GuiaRemision::orderBy("id","desc")->first();
        if($guia){
            $n_transaction = $guia->id + 1;
        }

        return response()->json([
            "clients" => ClientCollection::make($clients),
            "products" => ProductCollection::make($products),
            "today" => $today->format("Y-m-d"),
            "n_transaction" => str_pad($n_transaction, 8, "0", STR_PAD_LEFT),//00001000
        ]);
    }

    public function pdf($id) {
        $guia_remision = GuiaRemision::find($id);
        if(!$guia_remision){
            return abort(404);
        }
        $pdf = Pdf::loadView('pdf_guia_remision', compact('guia_remision'));
        return $pdf->stream('guia_remision_'.$id.'.pdf');
    }

    public function getcorrelativo() {
        $guia_remision = GuiaRemision::where("correlativo","<>",NULL)
                                            ->where("n_operacion","<>",NULL)
                                            ->orderBy("correlativo","desc")
                                            ->first();
        $correlativo = 1;
        if($guia_remision){
            $correlativo = $guia_remision->correlativo + 1;
        }
        return $correlativo;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $guia_remision_details = $request->guia_remision_details;

        try {
            DB::beginTransaction();

            $guia_remision = GuiaRemision::create([
                "serie" => $request->serie,
                // "correlativo",
                // "n_operacion",
                "user_id" => auth("api")->user()->id,
                "client_id" => $request->client_id,
                "type_client"  => $request->type_client,
                "total"  => $request->total,
                "quantity_total" => $request->quantity_total,
                "motivo_translado" => $request->motivo_translado,
                "type_transport"  => $request->type_transport,
                "description" => $request->description,
                "num_dam" => $request->num_dam,

                "punto_partida" => json_encode($request->punto_partida),
                "punto_llegada"  => json_encode($request->punto_llegada),
                "transporte_datos" => $request->transporte_datos ? json_encode($request->transporte_datos) : NULL,
                "conductor_datos" => $request->conductor_datos ? json_encode($request->conductor_datos) : NULL,
                // "cdr",
                // "xml",
            ]);
            
            foreach ($guia_remision_details as $key => $guia_remision_detail) {
                GuiaRemisionDetail::create([
                    "guia_remision_id" => $guia_remision->id,
                    "product_id" => $guia_remision_detail["product"]["id"],
                    "product_categorie_id" => $guia_remision_detail["product"]["categorie_id"],
                    "unidad_medida" =>  $guia_remision_detail["unidad_medida"],
                    "quantity" => $guia_remision_detail["quantity"],
                    "peso" => $guia_remision_detail["peso"],
                    "total" => $guia_remision_detail["total"],
                ]);
            }

            // EMITIR A LA SUNAT LA GUIA DE REMISION

            $company = Company::first();

            $seeApi = $this->greenter_service->getSeeApi();

            $data["tipo_doc"] = "09";
            $data["serie"] = $request->serie;
            $data["correlativo"] = $this->getCorrelativo();

            $guia_remision_emit = $this->greenter_service->getGuia($data,$company,$guia_remision);

            $result = $seeApi->send($guia_remision_emit);

            $response["success"] = $result->isSuccess();
            if(!$response["success"]){
                $response["error"] = [
                    "code" => $result->getError()->getCode(),
                    "message" => $result->getError()->getMessage(),
                ];
                return response()->json($response);
            }

            $ticket = $result->getTicket();
            // sleep(2);
            $res = $seeApi->getStatus($ticket);
            if(!$res->isSuccess()){
                $response["error"] = [
                    "code" => $res->getError()->getCode(),
                    "message" => $res->getError()->getMessage(),
                ];
                return response()->json($response);
            }

            $response = $this->greenter_service->sunatResponse($res);//DE GUARDAR EL cdr
            
            if(!isset($response['error'])){
                // Crear objeto DOMDocument
                $dom = new \DOMDocument('1.0');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($seeApi->getLastXml());
                // Formatear el XML con indentación
                $formattedXml = $dom->saveXML();
                // Generar un nombre único para el archivo
                $file_name = "TT-".env("RUC").'-'.$data['correlativo'].'-'.$guia_remision->serie."-".$guia_remision->id.' '.now()->format('Ymd_His'). '.xml';
                // Guardar en el disco storage/app/public/xml
                Storage::disk('public')->put('xml/' . $file_name, $formattedXml);
                // Obtener la ruta pública
                $public_path_xml = Storage::url('xml/' . $file_name);

                $guia_remision->update([
                    "correlativo" => $data['correlativo'],
                    "n_operacion" => $guia_remision->serie."-".$data['correlativo'],
                    "cdr" => $response['cdrZip'],
                    "xml" => $public_path_xml,
                ]);
                DB::commit();
                return response()->json([
                    "guia_remision" => GuiaRemisionResource::make($guia_remision),
                    "message" => "LA GUIA DE REMISIÓN SE HA REGISTRADO Y EMITIO CORRECTAMENTE",
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
