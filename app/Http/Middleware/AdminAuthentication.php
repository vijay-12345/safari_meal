<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuthentication
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
        // Provide access to admin only
        if ( Auth::user()->role_id != 1 && Auth::user()->role_id != 2 ) {
            return redirect('/');
        }
        
        return $next($request);
    }
}
