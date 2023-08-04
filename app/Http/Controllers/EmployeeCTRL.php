<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\EmployeeMODEL;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EmployeeCTRL extends Controller
{

    // SHOW USERS
    public function showUsers(){
        $userProfile = EmployeeMODEL::orderBy('account_id')
        ->with(['Users' => function($query){
            $query->select(['id', 'email']);
        }])
        ->with(['Role' => function($query){
            $query->select(['id', 'role_name', 'permission', 'status']);
        }])
        ->select(
            'id',
            'account_id',
            DB::raw("CONCAT(`firstname`, ' ', `lastname`) as `fullname`"),
            'contact',
            'address',
            'country',
            'state',
            'city',
            'zip_code',
            'department',
            'company',
            'role_id',
            'status'
        )->where("status", "active")->get();

        return response((array) [
            'data' => $userProfile,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

    // REGISTER USER ACCOUNT
    public function registerAccountProfile(UserRequest $request){
        try{
            DB::beginTransaction();

            $request->validated();
            $userAccount = User::create([
                'email' => $request->input('email'),
                'password' => Hash::make(env('DEFAULT_PASSWORD')),
            ], Response::HTTP_CREATED);

            if(!$userAccount){
                return response()->json([
                    "message" => "Account Creation Failed!",
                    "status" => "Failed"
                ], 422);
            }

            $userProfile = [
                'account_id' => $userAccount->id,
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'contact' => $request->input('contact'),
                'address' => $request->input('address'),
                'country' => $request->input('country'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'zip_code' => $request->input('zip_code'),
                'department' => $request->input('department'),
                'company' => $request->input('company'),
                'role_id' => $request->input('role_id'),
                'status' => 'active'
            ];

            $userAccount->Employee()->create($userProfile);

            DB::commit();

            $expiration = time() + 3600;
            $token = Token::create(
                $userAccount->id,
                env('SECRET_ID'),
                $expiration,
                $userAccount->email
            ); //generate jwt
            $cookie = cookie('jwt', $token, $expiration, null, null, true); //add cookie with jwt

            return response()->json([
                "accessToken" => $token,
                "message" => "User Created Successfully!",
                "status" => "Success"
            ], 201)->withCookie($cookie);

        }catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "Failed"
            ], 422);
        }
    }

    // USER PASSWORD CHANGE
    public function userPasswordChange(Request $request, $id){
        try{
            $userAccount = User::where("id", $id)->get('password')->first();
            $checkOldPassword = Hash::check($request->input('old_password'), $userAccount->password);

            if(!$checkOldPassword){
                return response()->json([
                    "message" => "Wrong Password!",
                    "status" => "Failed",
                ], 401);
            }

            if($request->input('new_password') != $request->input('confirm_password')){
                return response()->json([
                    "message" => "New password does not match!",
                    "status" => "Failed",
                ], 422);
            }

            $validation = Validator::make($request->all(),[
                'new_password' => [
                    'required',
                    Password::min(8)
                        ->letters()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()
                ],
                'confirm_password' => [
                    'required',
                    Password::min(8)
                        ->letters()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()
                ],
            ]);

            if($validation->fails()){
                return response()->json([
                    "message" => $validation->errors()->first(),
                    "status" => "Validation Failed",
                ], 422);
            }

            // $userAccount->update(array($request->input('new_password')));
            User::whereId($request->input('user_info.id'))->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                "message" => "Password Updated Successfully",
                "status" => "Success"
            ], Response::HTTP_OK);

        }catch(Throwable $e){
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "Failed"
            ], 422);
        }
    }

    // DISPLAY USER PROFILE
    public function showProfile(Request $request){
        $userID = $request->input('user_info.id');
        if(!$userID){
            return response()->json([
                "message" => "No User Found!",
                "status" => "Failed"
            ], 401);
        }

        $userProfile = EmployeeMODEL::where('account_id', $userID)
        ->with(['Users' => function($query){
            $query->select(['id', 'email']);
        }])
        ->with(['Role' => function($query){
            $query->select(['id', 'role_name', 'permission', 'status']);
        }])
        ->select(
            'id',
            'account_id',
            DB::raw("CONCAT(`firstname`, ' ', `lastname`) as `fullname`"),
            'contact',
            'address',
            'country',
            'state',
            'city',
            'zip_code',
            'department',
            'company',
            'role_id',
            'status'
        )->first();

        return response((array) [
            'data' => $userProfile,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

    // UPDATE USER PROFILE
    public function updateProfile(UserUpdateRequest $request, $id){
        try{
            $user = User::find($id);
            $userProfile = EmployeeMODEL::where('account_id', $id)->firstOrFail();

            if(!$user || !$userProfile){
                return response()->json([
                    "message" => "User not found!",
                    "status" => "User update failed!"
                ], 404);
            }
            $request->validated();

            $userInput = [
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'contact' => $request->input('contact'),
                'address' => $request->input('address'),
                'country' => $request->input('country'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'zip_code' => $request->input('zip_code'),
                'department' => $request->input('department'),
                'company' => $request->input('company'),
                'role_id' => $request->input('role_id'),
                'status' => 'active'
            ];

            $user->update($request->only('email'));
            $userProfile->update($userInput);

            return response()->json([
                "message" => "Account Updated Successfully",
                "status" => "Success"
            ], Response::HTTP_OK);

        }catch(Throwable $e){
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "Failed"
            ], 404);
        }
    }

    // ARCHIVE USER ACCOUNT
    public function archiveUser($id){
        try{
            DB::beginTransaction();
            $userAccount = User::findOrFail($id);
            $userProfile = EmployeeMODEL::where("account_id", $id)->first();

            if(!$userProfile || !$userAccount){
                return response()->json([
                    "message" => "No User Found to Archive!",
                    "status" => "Failed"
                ], 404);
            }

            $userProfile->delete();
            $userAccount->delete();

            DB::commit();

            return response()->json([
                "message" => "Account Archived Successfully!",
                "status" => "Success"
            ], Response::HTTP_OK);

        }catch(Throwable $e){
            DB::rollBack();
            return response()->json([
                "message" => "No User Found to Archive!",
                "status" => "Failed"
            ], 404);
        }
    }

    // SHOW ARCHIVED USERS
    public function showArchivedUsers(){
        $userProfile = EmployeeMODEL::onlyTrashed()
        ->with(['Users' => function($query){
            $query->select(['id', 'email']);
        }])
        ->with(['Role' => function($query){
            $query->select(['id', 'role_name', 'permission', 'status']);
        }])
        ->select(
            'id',
            'account_id',
            DB::raw("CONCAT(`firstname`, ' ', `lastname`) as `fullname`"),
            'contact',
            'address',
            'country',
            'state',
            'city',
            'zip_code',
            'department',
            'company',
            'role_id',
            'status'
        )->get();

        return response((array) [
            'data' => $userProfile,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

    // public function permanentDeleteUser($id){
    //     EmployeeMODEL::onlyTrashed()->findorfail($id)->forcedelete();
    //     return response()->json("Employee Permanently Deleted!");
    // }

    // RESTORE USER ACCOUNT
    public function restoreUser($id){
        try{
            DB::beginTransaction();
            $userAccount = User::onlyTrashed()->findOrFail($id);
            $userProfile = EmployeeMODEL::onlyTrashed()->where("account_id", $id)->first();

            if(!$userProfile || !$userAccount){
                return response()->json([
                    "message" => "No User Found to Restore!",
                    "status" => "Failed"
                ], 404);
            }

            $userProfile->restore();
            $userAccount->restore();

            DB::commit();

            return response()->json([
                "message" => "Account Restored Successfully!",
                "status" => "Success"
            ], Response::HTTP_OK);

        }catch(Throwable $e){
            DB::rollBack();
            return response()->json([
                "message" => "No User Found to Restore!",
                "status" => "Failed"
            ], 404);
        }
    }
}
