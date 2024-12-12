<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $roles = explode('|', $roles); // Split the roles string by "|"

        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            // Redirect non-matching roles
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
