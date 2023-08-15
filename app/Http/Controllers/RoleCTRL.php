<?php

namespace App\Http\Controllers;

use App\Models\RoleMODEL;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class RoleCTRL extends Controller
{
    // SHOW ROLES
    public function showRoles(){
        $rolesList = RoleMODEL::orderBy('role_name')
        ->where('id', '!=' , 0)->get();

        return response((array) [
            'data' => $rolesList,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

    // SHOW ACTIVE ROLES ONLY
    public function showActiveRoles(){
        $rolesList = RoleMODEL::orderBy('role_name')
        ->where('id', '!=' , 0)
        ->where("status", "active")->get();

        return response((array) [
            'data' => $rolesList,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

    // CREATE ROLE
    public function createRole(Request $request){
        try{
            DB::beginTransaction();

            $validation = Validator::make($request->all(),[
                'role_name' => ['required', 'unique:tbl_roles,role_name'],
                'permission' => 'required',
            ]);

            if($validation->fails()){
                return response()->json([
                    "message" => $validation->errors()->first(),
                    "status" => "Validation Failed",
                ], 422);
            }

            $roles = RoleMODEL::create([
                'role_name' => $request->input('role_name'),
                'permission' => serialize($request->input('permission')),
                'status' => 'active'
            ], 200);

            if(!$roles){
                return response()->json([
                    "message" => "Role Creation Failed!",
                    "status" => "Failed"
                ], 422);
            }

            DB::commit();

            return response()->json([
                "message" => "Role Created Successfully!",
                "status" => "Success"
            ], 201);

        }catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "Failed"
            ], 422);
        }
    }

    // DISPLAY SELECTED ROLE
    public function showSelectedRole($id){
        $roleID = $id;

        if(!$roleID){
            return response()->json([
                "message" => "No Role Found!",
                "status" => "Failed"
            ], 401);
        }

        $role = RoleMODEL::where('id', $roleID)->first();

        return response((array) [
            'data' => $role,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

    // UPDATE ROLE
    public function updateRole(Request $request, $id){
        try{
            $role = RoleMODEL::find($id);
            if(!$role){
                return response()->json([
                    "message" => "Role not found!",
                    "status" => "Role update failed!"
                ], 404);
            }

            $validation = Validator::make($request->all(),[
                'role_name' => [
                    'required',
                    Rule::unique('tbl_roles')->where(function ($query) use ($request) {
                        return $query->where('role_name', $request->role_name)
                                    ->where('id', '!=', $request->id);
                    })
                ],
                'permission' => 'required',
                'status' => 'required',
            ]);

            if($validation->fails()){
                return response()->json([
                    "message" => $validation->errors()->first(),
                    "status" => "Validation Failed",
                ], 422);
            }

            $userInput = [
                'role_name' => $request->input('role_name'),
                'permission' => serialize($request->input('permission')),
                'status' => $request->input('status'),
            ];

            $role->update($userInput);

            return response()->json([
                "message" => "Role Updated Successfully",
                "status" => "Success"
            ], 202);

        }catch(Throwable $e){
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "Failed"
            ], 404);
        }
    }

    // ARCHIVE ROLE
    public function archiveRole($id){
        try{
            DB::beginTransaction();
            $role = RoleMODEL::findOrFail($id);

            if(!$role){
                return response()->json([
                    "message" => "No Role Found to Archive!",
                    "status" => "Failed"
                ], 404);
            }

            $role->update(array('status' => 'archived'));
            $role->delete();

            DB::commit();

            return response()->json([
                "message" => "Role Archived Successfully!",
                "status" => "Success"
            ], 202);

        }catch(Throwable $e){
            DB::rollBack();
            return response()->json([
                "message" => "No Role Found to Archive!",
                "status" => "Failed"
            ], 404);
        }
    }

    // SHOW ARCHIVED ROLES
    public function showArchivedRoles(){
        $archivedRole = RoleMODEL::onlyTrashed()->get();

        if(!$archivedRole){
            return response()->json([
                "message" => "No Role Found in Archive!",
                "status" => "Failed"
            ], 404);
        }

        return response((array) [
            'data' => $archivedRole,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

    // RESTORE ROLE
    public function restoreRole($id){
        try{
            DB::beginTransaction();
            $role = RoleMODEL::onlyTrashed()->findOrFail($id);

            if(!$role){
                return response()->json([
                    "message" => "No Role Found to Restore!",
                    "status" => "Failed"
                ], 404);
            }

            $role->update(array('status' => 'active'));
            $role->restore();

            DB::commit();

            return response()->json([
                "message" => "Role Restored Successfully!",
                "status" => "Success"
            ], 202);

        }catch(Throwable $e){
            DB::rollBack();
            return response()->json([
                "message" => "No Role Found to Restore!",
                "status" => "Failed"
            ], 404);
        }
    }

}
