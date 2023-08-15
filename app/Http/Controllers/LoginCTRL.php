<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LoginCTRL extends Controller
{
    public function index(){
        return response()->json("Login");
    }

    public function login(Request $request){

        $validation = Validator::make($request->all(),[
            'email' => [
                'required', "email"
            ],
            'password' => 'required',
        ]);

        if($validation->fails()){
            return response()->json([
                "message" => $validation->errors()->first(),
                "status" => "Validation Failed",
            ], 422);
        }

        if(!auth()->attempt($request->only('email','password'),$request->remember)){
            return response()->json([
                "message" => 'Invalid Credentials!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if(Auth::user()->status != 'active'){
            return response()->json([
                "message" => 'Account inactive, please contact the Admin!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $expiration = time() + 86400;
        $token = Token::create(
            Auth::user()->id,
            env('SECRET_ID'),
            $expiration,
            Auth::user()->email
        ); //generate jwt
        $cookie = cookie('jwt', $token, $expiration, null, null, true); //add cookie with jwt

        return response()->json([
            "accessToken" => $token,
            // "user" => Auth::user(),
        ], Response::HTTP_OK)
        ->withCookie($cookie);
    }

    // REGISTER USER ACCOUNT OUTSIDE
    public function registerAccount(Request $request){
        try{
            DB::beginTransaction();

            $validation = Validator::make($request->all(),[
                'email' => [
                    'required',
                    'unique:users,email',
                    'email',
                ],
                'firstname' => ['required'],
                'lastname' => ['required'],
                'password' => [
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

            $userAccount = User::create([
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'status' => 'active'
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
                'role_id' => 1,
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

}
