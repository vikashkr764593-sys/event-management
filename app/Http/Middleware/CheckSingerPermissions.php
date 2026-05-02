<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
 
class CheckSingerPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If it's a read-only request, allow everyone (who passed auth:sanctum)
        if ($request->isMethod('get')) {
            return $next($request);
        }
 
        // 2. If it's a write operation, check the role
        if (Auth::check() && Auth::user()->role === 'viewer') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You only have read-only access (Viewer role).'
                ], 403);
            }
 
            return redirect()->back()->withErrors([
                'error' => 'Unauthorized: You only have read-only access (Viewer role).'
            ]);
        }
 
        return $next($request);
    }
}
