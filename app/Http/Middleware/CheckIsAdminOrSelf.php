<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
class CheckIsAdminOrSelf
{
    public function handle($request, Closure $next)
    {
        $requestedUserId = $request->route()->parameter('id');
        if(
            Auth::user()->role === 'admin' ||
            //Auth::user()->role === 'root' ||
            //Auth::user()->role === 'user' ||
            Auth::user()->id == $requestedUserId
        ) {
            return $next($request);
        }
        else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
}
