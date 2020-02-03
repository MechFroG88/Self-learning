<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class Authenticate 
{

    public function handle($request, Closure $next)
    {
        if(Auth::check()){
            return $next($request);
        }
        return response("Unauthorized",401);
    }
}
