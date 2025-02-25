<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/dashboard')->with('error', 'Access denied');
        }

        // Check if user role is in the allowed roles
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // Redirect to dashboard if access is denied
        return redirect('/dashboard')->with('error', 'Access denied');
    }
}
