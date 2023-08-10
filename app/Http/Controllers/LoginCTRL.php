<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Response;

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

}
