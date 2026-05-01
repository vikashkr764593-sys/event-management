<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Singer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSingerController extends Controller {
    
    public function index(Request $request) {
        $query = Singer::with('user')->latest();

        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        $singers = $query->get();
        
        // Get unique genres for the filter dropdown
        $genres = Singer::whereNotNull('genre')->distinct()->pluck('genre');

        return view('admin.singers.index', compact('singers', 'genres'));
    }

    public function create() { 
        $users = User::all();
        return view('admin.singers.create', compact('users')); 
    }

    public function store(Request $request) { 
        $validated = $request->validate([
            'user_id'             => 'required|exists:users,id',
            'name'                => 'required|string|max:255',
            'stage_name'          => 'nullable|string|max:255',
            'genre'               => 'nullable|string|max:100',
            'experience_years'    => 'nullable|integer|min:0',
            'availability_status' => 'required|string|in:available,unavailable,busy',
            'rating'              => 'nullable|numeric|min:0|max:5',
            'fee'                 => 'nullable|numeric|min:0',
            'biography'           => 'nullable|string',
            'profile_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('singers', 'public');
        }

        Singer::create($validated);

        return redirect()->route('admin.singers.index')->with('success', 'Singer created successfully.');
    }

    public function edit($id) {
        $singer = Singer::findOrFail($id);
        $users = User::all();
        return view('admin.singers.edit', compact('singer', 'users'));
    }

    public function update(Request $request, $id) { 
        $singer = Singer::findOrFail($id);

        $validated = $request->validate([
            'user_id'             => 'required|exists:users,id',
            'name'                => 'required|string|max:255',
            'stage_name'          => 'nullable|string|max:255',
            'genre'               => 'nullable|string|max:100',
            'experience_years'    => 'nullable|integer|min:0',
            'availability_status' => 'required|string|in:available,unavailable,busy',
            'rating'              => 'nullable|numeric|min:0|max:5',
            'fee'                 => 'nullable|numeric|min:0',
            'biography'           => 'nullable|string',
            'profile_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if it exists
            if ($singer->profile_image) {
                Storage::disk('public')->delete($singer->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('singers', 'public');
        }

        $singer->update($validated);

        return redirect()->route('admin.singers.index')->with('success', 'Singer updated successfully.');
    }

    public function destroy($id) {
        $singer = Singer::findOrFail($id);
        
        if ($singer->profile_image) {
            Storage::disk('public')->delete($singer->profile_image);
        }

        $singer->delete();

        return redirect()->route('admin.singers.index')->with('success', 'Singer deleted successfully.');
    }
}