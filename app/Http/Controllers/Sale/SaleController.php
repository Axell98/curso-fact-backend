<?php

namespace App\Http\Controllers\Sale;

use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientCollection;
use App\Http\Resources\Product\ProductCollection;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function config(){

        $today = today();
        $clients = Client::where("state",1)->orderBy("full_name","asc")->get();
        $products = Product::where("state",1)->where("is_especial_nota",0)->orderBy("title","asc")->get();

        $n_transaction = 1000;
        $sale = Sale::orderBy("id","desc")->first();
        if($sale){
            $n_transaction = $sale->id + 1;
        }

        return response()->json([
            "clients" => ClientCollection::make($clients),
            "products" => ProductCollection::make($products),
            "today" => $today->format("Y-m-d"),
            "n_transaction" => str_pad($n_transaction, 8, "0", STR_PAD_LEFT),//00001000
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
