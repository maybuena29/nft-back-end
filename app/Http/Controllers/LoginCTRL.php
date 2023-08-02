<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\EmployeeMODEL;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LoginCTRL extends Controller
{
    public function index(){
        return response()->json("Login");
    }

    public function register_user(Request $request){
        try{
            $validation = Validator::make($request->all(),[
                'username' => [
                    'required',
                    'unique:users,username',
                ],
                'email' => [
                    'required',
                    'unique:users,email',
                    "email",
                ],
                'fullname' => 'required',
                'password' => [
                    'required',
                    Password::min(8)
                        ->letters()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),
                ],
            ]);

            if($validation->fails()){
                return response()->json([
                    "message" => $validation->errors()->first(),
                    "status" => "Validation Failed",
                ], 422);
            }

            $user = User::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'fullname' => $request->input('fullname'),
                'password' => Hash::make($request->input('password')),
            ], Response::HTTP_CREATED);

            $expiration = time() + 1440;
            $token = Token::create(
                $user->id,
                env('SECRET_ID'),
                $expiration,
                $user->username
            ); //generate jwt
            $cookie = cookie('jwt', $token, $expiration, null, null, true); //add cookie with jwt

            return response()->json([
                "accessToken" => $token,
                "message" => "User Created Successfully!",
                "status" => "Success"
            ], 201)->withCookie($cookie);

        }catch (Throwable $e) {
            return response()->json(["message" => $e->getMessage(), "status" => "Failed On Create"], 422);
        }

    }

    public function verifyAuth(Request $request){
        dd($request->cookie('jwt'));
    }

    public function login(Request $request){
        $validation = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validation->fails()){
            return response()->json([
                "message" => $validation->errors()->first(),
                "status" => "Validation Failed",
            ], 422);
        }

        if(!auth()->attempt($request->only('username','password'),$request->remember)){
            return response()->json(["message" => 'Invalid Credentials!'], Response::HTTP_UNAUTHORIZED);
        }

        $expiration = time() + 1440;
        $token = Token::create(
            Auth::user()->id,
            env('SECRET_ID'),
            $expiration,
            Auth::user()->username
        ); //generate jwt
        $cookie = cookie('jwt', $token, $expiration, null, null, true); //add cookie with jwt

        return response()->json([
            "accessToken" => $token,
        ], Response::HTTP_OK)->withCookie($cookie);
    }

    public function user_profile(Request $request){
        //Display user profile
        $userID = $request->input('user_info.id');
        if(!$userID){
            return response()->json(["message" => "No User Found!"], 401);
        }

        $user = User::where('id', $userID)
        ->select('id', 'username', 'fullname', 'email')->first();

        return response((array) [
            'data' => $user,
            'status' => 'success'
        ], 200)
            ->header('Content-Type', 'application/json');
    }
}
