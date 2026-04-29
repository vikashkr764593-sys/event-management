<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('pages.profile', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the account settings page.
     */
    public function settings()
    {
        $user = Auth::user();
        return view('pages.profile.settings', compact('user'));
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'          => ['required'],
            'password'                  => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => $request->password]);

        return redirect()->route('admin.settings')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Show the support page.
     */
    public function support()
    {
        return view('pages.profile.support');
    }
}
