<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
            // dd($request);
        }
    }

    // FOR COOKIE AUTHENTICATION
    // public function handle($request, Closure $next, ...$guards)
    // {
    //     // if($jwt = $request->cookie('jwt')){
    //     //     $request->headers->set('Authorization', 'Bearer ' . $jwt);
    //     // }
    //     $this->authenticate($request, $guards);

    //     return $next($request);
    // }

    // public function handle($request, Closure $next, ...$guards)
    // {
    //     if ($request->api_token != env('API_KEY')) {
    //         return response()->json('Unauthorized', 401);
    //     }

    //     return $next($request);
    // }
}
