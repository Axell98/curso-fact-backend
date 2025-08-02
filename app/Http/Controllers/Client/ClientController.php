<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\Client\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Client\ClientCollection;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ?search=
        $search = $request->get("search");

        $clients = Client::where(DB::raw("CONCAT(clients.full_name,' ',IFNULL(clients.phone,''),' ',
                    IFNULL(clients.email,''),' ',IFNULL(clients.n_document,''))"),"like","%".$search."%")->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $clients->total(),
            "paginate" => 25,
            "clients" => ClientCollection::make($clients),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $is_exits_clients = Client::where("full_name",$request->full_name)->first();
        if($is_exits_clients){
            return response()->json([
                "code" => 405,
                "message" => "EL NOMBRE CLIENTE YA EXISTE"
            ]);
        }
        $is_exits_clients = Client::where("n_document",$request->n_document)->first();
        if($is_exits_clients){
            return response()->json([
                "code" => 405,
                "message" => "EL N° DEL CLIENTE YA EXISTE"
            ]);
        }
        $request->request->add(["user_id" => auth('api')->user()->id]);
        $client = Client::create($request->all());

        return response()->json([
            "code" => 200,
            "message" => "EL CLIENTE SE HA REGISTRADO CORRECTAMENTE",
            "client" => ClientResource::make($client)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $is_exits_clients = Client::where("id","<>",$id)->where("full_name",$request->full_name)->first();
        if($is_exits_clients){
            return response()->json([
                "code" => 405,
                "message" => "EL NOMBRE CLIENTE YA EXISTE"
            ]);
        }
        $is_exits_clients = Client::where("id","<>",$id)->where("n_document",$request->n_document)->first();
        if($is_exits_clients){
            return response()->json([
                "code" => 405,
                "message" => "EL N° DEL CLIENTE YA EXISTE"
            ]);
        }

        $client = Client::findOrFail($id);
        $client->update($request->all());
        return response()->json([
            "code" => 200,
            "message" => "EL CLIENTE SE HA EDITADO CORRECTAMENTE",
            "client" => ClientResource::make($client)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json([
            "code" => 200,
            "message" => "EL CLIENTE SE HA ELIMINADO CORRECTAMENTE",
        ]);
    }
}
