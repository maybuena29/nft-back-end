<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Token;

class TokenAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        try{
            if (!str_starts_with($request->headers->get('Authorization'), "Bearer ")){
                return response()->json(['message' => 'Invalid Authorization'], 401);
            }
            $tokenArray = explode(" ", $request->headers->get('Authorization'));
            $validateToken = Token::validate($tokenArray[1], env('SECRET_ID'));

            if(!$validateToken){
                return response()->json(['message' => 'Invalid Token'], 401);
            }

            $user = Token::getPayload($tokenArray[1], env('SECRET_ID')); //getting user info using token
            $userinfo = User::find($user)->first();

            if(!$userinfo){
                return response()->json(['message' => 'No User Found!'], 401);
            }

            $request->merge(['user_info' => [
                'id' => $userinfo->id,
                'username' => $userinfo->username,
                'fullname' => $userinfo->fullname,
                'email' => $userinfo->email,
            ]]);

        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
