<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReallySimpleJWT\Token;
use Symfony\Component\HttpFoundation\Response;

class UserAPIAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->headers->get('x-api-key') != env('SECRET_API_KEY') || !$request->headers->get('x-api-key')) {
            return response()->json(['message' => 'Invalid API Key'], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }

}
