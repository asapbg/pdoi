<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ... $roles)
    {
        $guard = currentGuard();
        $user = Auth::guard($guard)->user();

        if ($roles[0] == "all") {
            return $next($request);
        }

        foreach($roles as $role) {
            if($user->can($role)) {
                return $next($request);
            }
        }

        return redirect()->back()->with('warning', trans('messages.no_rights_to_view_content'));
    }
}
