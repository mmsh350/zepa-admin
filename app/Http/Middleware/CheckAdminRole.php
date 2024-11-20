<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {

        // Check if the authenticated user has the "agent" role
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Optionally, add a different response if the role doesn't match
        // For example, redirect to a different page or return a different error

        // If the user is not authenticated or does not have the "agent" role, return a 403 response
        return abort(403, 'Access denied');
    }
}
