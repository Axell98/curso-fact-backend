<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get("search"); // 654654
        // 'jose jaico jo@gmail.com ' jaico
        // sofia martinez so@gmail.com  654654654
        $users = User::where(DB::raw("CONCAT(users.name,' ',
                    IFNULL(users.surname,''),' ',IFNULL(users.phone,''),' ',
                    users.email,' ',IFNULL(users.n_document,''))"),"like","%".$search."%")
                    ->orderBy("id","desc")->paginate(25);
        $roles = Role::all();
        return response()->json([
            "total" => $users->total(),
            "paginate" => 25,
            "users" => UserCollection::make($users),
            "roles" => $roles->map(function($role) {
                return [
                    "id" => $role->id,
                    "name" => $role->name
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $is_exits_user = User::where("email",$request->email)->first();

        if($is_exits_user){
            return response()->json([
                "code" => 405,
                "message" => "El email para este usuario ya existe"  
            ]);
        }

        if($request->hasFile("imagen")){
            $path = Storage::putFile("users",$request->file("imagen"));
            $request->request->add(["avatar" => $path]);
        }

        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }

        $user = User::create($request->all());
        $role = Role::find($request->role_id);
        $user->assignRole($role);

        return response()->json([
            "code" => 200,
            "message" => "El usuario se registro correctamente",
            "user" => UserResource::make($user),
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
        $is_exits_user = User::where("id","<>",$id)->where("email",$request->email)->first();

        if($is_exits_user){
            return response()->json([
                "code" => 405,
                "message" => "El email para este usuario ya existe"  
            ]);
        }

        $user = User::findOrFail($id);

        if($request->hasFile("imagen")){
            if($user->avatar){
                Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users",$request->file("imagen"));
            $request->request->add(["avatar" => $path]);
        }

        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }
        
        if($user->role_id != $request->role_id){
            $role_current = Role::find($user->role_id);
            $user->removeRole($role_current);

            $role_new = Role::find($request->role_id);
            $user->assignRole($role_new);
        }

        $user->update($request->all());

        return response()->json([
            "code" => 200,
            "message" => "El usuario se actualizado correctamente",
            "user" => UserResource::make($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if($user->avatar){
            Storage::delete($user->avatar);
        }
        $user->delete();
        return response()->json([
            "code" => 200,
            "message" => "El usuario se ha eliminado correctamente",
        ]);
    }
}
