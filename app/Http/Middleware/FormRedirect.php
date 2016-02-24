<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class FormRedirect
{
    /**
     * preventing users from accessing property steps without going through all previous steps
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //always add auth middleware instead to avoid repeating code

/*        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }*/

        dd($propertySession);
        if(isset($request->propertySession))
        {
            //preventing non admin users from accessing admin pages
            return redirect()->to('/addproperty');
        }

        return $next($request);
    }
}

?>
