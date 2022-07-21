<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIsValidated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
     //   return response()->json(['user' => Auth::user()], 200);
        if(Auth::user()->validated == true) {
            return $next($request);
        }
        else {
            return response()->json(['message' => 'please add your contacts and validate it'], 200);
        }
    }
}
