<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
 
class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Please log in to access the control panel.',
            ]);
        }
 
        // 2. Strictly permit only 'admin' role
        if (Auth::user()->role !== 'admin') {
            // Log the user out immediately
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
 
            // Return 403 Forbidden or redirect to a landing page as requested
            return redirect()->route('admin.login')->withErrors([
                'email' => '403 Forbidden: User accounts are restricted to the mobile app. Web login is for Administrators only.',
            ]);
        }
 
        return $next($request);
    }
}
