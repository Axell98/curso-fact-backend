<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Product\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // search
        // categorie_id
        // state
        // unidad_medida
        $search = $request->get("search");
        $categorie_id = $request->get("categorie_id");
        $state = $request->get("state");
        $unidad_medida = $request->get("unidad_medida");
        // 
        $products = Product::filterMultiple($search,$categorie_id,$state,$unidad_medida)->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $products->total(),
            "paginate" => 25,
            "products" => ProductCollection::make($products)
        ]);
    }

    public function config() {
        $categories = Categorie::where("state",1)->get();
        return response()->json([
            "categories" => $categories->map(function($categorie) {
                return [
                    "id" => $categorie->id,
                    "title" => $categorie->title,
                ];
            })
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $is_product_title = Product::where("title",$request->title)->first();
        if($is_product_title){
            return response()->json([
                "code" => 405,
                "message" => "El titulo del producto ya existe"
            ]);
        }
        $is_product_sku = Product::where("sku",$request->sku)->first();
        if($is_product_sku){
            return response()->json([
                "code" => 405,
                "message" => "El sku del producto ya existe"
            ]);
        }
        
        if($request->hasFile("image")){
            $path = Storage::putFile("products",$request->file("image"));
            $request->request->add(["imagen" => $path]);
        }

        $product = Product::create($request->all());

        return response()->json([
            "code" => 200,
            "message" => "El producto se ha creado correctamente"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if(!$product){
            return response()->json([
                "code" => 405,
                "message" => "El producto no existe"
            ]);
        }

        return response()->json([
            "product" => ProductResource::make($product),
            "code" => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $is_product_title = Product::where("id","<>",$id)->where("title",$request->title)->first();
        if($is_product_title){
            return response()->json([
                "code" => 405,
                "message" => "El titulo del producto ya existe"
            ]);
        }
        $is_product_sku = Product::where("id","<>",$id)->where("sku",$request->sku)->first();
        if($is_product_sku){
            return response()->json([
                "code" => 405,
                "message" => "El sku del producto ya existe"
            ]);
        }
        $product = Product::findOrFail($id);

        if($request->hasFile("image")){
            if($product->imagen){
                Storage::delete($product->imagen);
            }
            $path = Storage::putFile("products",$request->file("image"));
            $request->request->add(["imagen" => $path]);
        }

        $product->update($request->all());

        return response()->json([
            "code" => 200,
            "message" => "El producto se ha actualizado correctamente"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        // if($product->imagen){
        //     Storage::delete($product->imagen);
        // }
        $product->delete();
        return response()->json([
            "code" => 200,
            "message" => "El producto se ha eliminado correctamente"
        ]);
    }
}
