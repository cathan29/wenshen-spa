<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in AND has the 'admin' role
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // If not, kick them back to the Receptionist Dashboard
        return redirect()->route('dashboard')->with('error', 'Authorized Personnel Only.');
    }
}