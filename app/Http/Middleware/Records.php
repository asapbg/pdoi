<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class Records
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guard = currentGuard();

        if (Auth::guard($guard)->check() && Auth::guard($guard)->user() instanceof \App\Models\User
            && (role_is('records'))) {
            return $next($request);
        }

        return to_route('home')->with('warning', trans('messages.no_rights_to_view_content'));
    }
}
