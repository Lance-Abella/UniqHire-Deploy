<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Ensure the user is authenticated
        if (!$request->user()) {
            return redirect()->route('home'); // Redirect to a default route for unauthenticated users
        }

        // Check if the user has at least one of the required roles
        if (collect($roles)->contains(fn($role) => $request->user()->hasRole($role))) {
            return $next($request); // Allow the request to proceed
        }

        // Deny access if no matching role is found
        return redirect()->route('home')->with('error', 'You are not authorized to access this page.');
    }
}
