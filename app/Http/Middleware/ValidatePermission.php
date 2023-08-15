<?php

namespace App\Http\Middleware;

use App\Models\EmployeeMODEL;
use Closure;
use Illuminate\Http\Request;
use ReallySimpleJWT\Token;

class ValidatePermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $tokenArray = explode(" ", $request->headers->get('Authorization'));

        $user = Token::getPayload($tokenArray[1], env('SECRET_ID')); //getting user info using token
        $userinfo = EmployeeMODEL::with('Role')->find($user)->first();

        if(!$userinfo){
            return response()->json([
                'message' => 'No User Found From the Token!',
                'status' => 'Failed'
            ], 401);
        }

        if(!in_array($permission, $userinfo->role->permission)){
            return response()->json([
                'message' => 'You have no permission to do this action!',
                'status' => 'Failed'
            ], 403);
        }

        return $next($request);
    }
}
