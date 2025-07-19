<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //?search=tv
        $search = $request->get("search");
        $categories = Categorie::where("title","like","%".$search."%")->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $categories->total(),
            "paginate" => 25,
            "categories" => $categories->map(function($categorie){
                return [
                    "id" => $categorie->id,
                    "title" => $categorie->title,
                    "imagen" => env("APP_URL")."storage/".$categorie->imagen,
                    "state" => $categorie->state,
                    "created_at" => $categorie->created_at->format("Y/m/d h:i A")
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $is_exits = Categorie::where("title",$request->title)->first();
        if($is_exits){
            return response()->json([
                "code" => 405,
                "message" => "LA CATEGORIA YA EXISTE"
            ]);
        }
        if($request->hasFile("image")){
            $path = Storage::putFile("categories",$request->file("image"));
            $request->request->add(["imagen" => $path]);
        }
        $categorie = Categorie::create($request->all());

        return response()->json([
            "code" => 200,
            "message" => "La categoria se registro correctamente",
            "categorie" => [
                "id" => $categorie->id,
                "title" => $categorie->title,
                "imagen" => env("APP_URL")."storage/".$categorie->imagen,
                "state" => $categorie->state,
                "created_at" => $categorie->created_at->format("Y/m/d h:i A")
            ]
        ]);
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
        $is_exits = Categorie::where("id","<>",$id)->where("title",$request->title)->first();
        if($is_exits){
            return response()->json([
                "code" => 405,
                "message" => "LA CATEGORIA YA EXISTE"
            ]);
        }
        $categorie = Categorie::find($id);
        if($request->hasFile("image")){
            if($categorie->imagen){
                Storage::delete($categorie->imagen);
            }
            $path = Storage::putFile("categories",$request->file("image"));
            $request->request->add(["imagen" => $path]);
        }
        
        $categorie->update($request->all());
        return response()->json([
            "code" => 200,
            "message" => "La categoria se edito correctamente",
            "categorie" => [
                "id" => $categorie->id,
                "title" => $categorie->title,
                "imagen" => env("APP_URL")."storage/".$categorie->imagen,
                "state" => $categorie->state,
                "created_at" => $categorie->created_at->format("Y/m/d h:i A")
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categorie = Categorie::find($id);
        $categorie->delete();
         return response()->json([
            "code" => 200,
            "message" => "La categoria se elimino correctamente",
        ]);
    }
}
