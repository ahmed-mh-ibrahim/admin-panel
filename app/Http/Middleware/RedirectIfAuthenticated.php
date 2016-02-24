<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            //return redirect('/index.html#/core/login');
            //return Auth::user();
        }

        return $next($request);

        /*
        if (!Auth::check()/) {
            return redirect('/index.html#/core/login');
        }

        return $next($request);
        */
    }
}
