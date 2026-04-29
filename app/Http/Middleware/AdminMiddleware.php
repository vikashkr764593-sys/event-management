<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the user is authenticated at all
        if (!Auth::check()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Please log in to access the control panel.',
            ]);
        }

        // 2. Check if the authenticated user has the 'admin' role
        if (Auth::user()->role !== 'admin') {
            // Not an admin: log them out and redirect
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Access denied. You do not have administrator privileges.',
            ]);
        }

        // 3. User is an admin, proceed
        return $next($request);
    }
}
