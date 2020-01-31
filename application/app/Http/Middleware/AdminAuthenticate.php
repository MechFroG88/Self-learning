<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class AdminAuthenticate
{

    public function handle($request, Closure $next)
    {
        if(Auth::check() && Auth::user()->type == 0){
            return $next($request);
        }
        return response("Unauthorized",401);
    }
}