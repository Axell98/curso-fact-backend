<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // /api/roles?search=asesor
        $search = $request->get("search");
        $roles = Role::where("name","like","%".$search."%")->orderBy("id","desc")->paginate(15);

        return response()->json([
            "total" => $roles->total(),
            "paginate" => 15,
            "roles" => $roles->map(function($role) {
                return [
                    "id" => $role->id,
                    "name" => $role->name,
                    "created_at" => $role->created_at->format("Y/m/d h:i A"),
                    "permissions" => $role->permissions->map(function($permission) {
                        return [
                            "id" => $permission->id,
                            "name" => $permission->name
                        ];
                    }),//["register_role","edit_role"]
                    "permissions_pluck" => $role->permissions->pluck("name"),
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $is_exits_role = Role::where("name",$request->name)->first();

        if($is_exits_role){
            return response()->json([
                "code" => 405,
                "message" => "El nombre del rol ya existe, intente con uno nuevo"
            ]);
        }

        $role = Role::create([
            "name" => $request->name,
            "guard_name" => "api"
        ]);

        $permissions = $request->permissions;
        foreach ($permissions as $key => $permission) {
            $role->givePermissionTo($permission);
        }

        return response()->json([
            "code" => 200,
            "message" => "El rol se ha creado correctamente",
            "role" => [
                "id" => $role->id,
                "name" => $role->name,
                "created_at" => $role->created_at->format("Y/m/d h:i A"),
                "permissions" => $role->permissions->map(function($permission) {
                    return [
                        "id" => $permission->id,
                        "name" => $permission->name
                    ];
                }),//["register_role","edit_role"]
                "permissions_pluck" => $role->permissions->pluck("name"),
            ],
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
        $is_exits_role = Role::where("id","<>",$id)->where("name",$request->name)->first();

        if($is_exits_role){
            return response()->json([
                "code" => 405,
                "message" => "El nombre del rol ya existe, intente con uno nuevo"
            ]);
        }

        $role = Role::findOrFail($id);
        $role->update([
            "name" => $request->name,
        ]);

        $permissions = $request->permissions;
        $role->syncPermissions($permissions);

        return response()->json([
            "code" => 200,
            "message" => "El rol se ha editado correctamente",
            "role" => [
                "id" => $role->id,
                "name" => $role->name,
                "created_at" => $role->created_at->format("Y/m/d h:i A"),
                "permissions" => $role->permissions->map(function($permission) {
                    return [
                        "id" => $permission->id,
                        "name" => $permission->name
                    ];
                }),//["register_role","edit_role"]
                "permissions_pluck" => $role->permissions->pluck("name"),
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            "code" => 200,
            "message" => "El rol se ha eliminado correctamente"
        ]);
    }
}
