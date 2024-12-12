<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // Check if the user's role is 'super-admin'
            if (auth()->user()->role === 'super-admin') {
                return $next($request);
            } else {
                // Redirect non-super-admin users to '/dashboard'
                return redirect('/dashboard');
            }
        }

        return redirect('/');
    }
}
