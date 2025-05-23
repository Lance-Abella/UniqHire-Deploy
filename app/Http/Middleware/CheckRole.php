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
        if (!$request->user()) {
            return redirect()->route('home');
        }

        if (collect($roles)->contains(fn($role) => $request->user()->hasRole($role))) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'You are not authorized to access this page.');
    }
}
