<?php
 
namespace App\Http\Controllers\Web;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
 
        return view('pages.auth.signin', ['title' => 'Admin Login']);
    }
 
    /**
     * Handle admin login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);
 
        if (Auth::attempt($credentials)) {
            // Check if user is an admin
            if (Auth::user()->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                // Not an admin, log them out and reject
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
 
                return redirect()->route('admin.login')->withErrors([
                    'email' => '403 Forbidden: User accounts are restricted to the mobile app. Web login is for Administrators only.',
                ]);
            }
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
 
    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect()->route('admin.login');
    }
}
