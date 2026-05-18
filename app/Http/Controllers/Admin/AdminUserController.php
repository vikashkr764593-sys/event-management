<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller {
    
    public function index() {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create() { 
        return view('admin.users.create'); 
    }

    public function store(Request $request) { 
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'city'     => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'role'     => 'required|string|in:admin,user',
            'status'   => 'required|string|in:active,inactive',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'phone'    => $validated['phone'] ?? null,
                    'city'     => $validated['city'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'role'     => $validated['role'],
                    'status'   => $validated['status'],
                ]);
            });

            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function edit($id) {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id) { 
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'city'     => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
            'role'     => 'required|string|in:admin,user',
            'status'   => 'required|string|in:active,inactive',
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                $user->name   = $validated['name'];
                $user->email  = $validated['email'];
                $user->phone  = $validated['phone'] ?? null;
                $user->city   = $validated['city'] ?? null;
                $user->role   = $validated['role'];
                $user->status = $validated['status'];
                
                if (!empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                }

                $user->save();
            });

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function destroy($id) {
        try {
            DB::transaction(function () use ($id) {
                $user = User::findOrFail($id);
                // Prevent user from deleting themselves if desired, not strictly required here
                $user->delete();
            });
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to delete user.');
        }
    }
}