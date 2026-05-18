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
        $users = User::where('role', 'user')->get();
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
            'languages'           => 'nullable|string', // Treat as comma separated or string in admin panel initially, then split or just array if multiple
            'travel_available'    => 'boolean',
            'instagram_link'      => 'nullable|url',
            'youtube_link'        => 'nullable|url',
            'spotify_link'        => 'nullable|url',
            'cover_image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sample_audio'        => 'nullable|mimes:mp3,wav|max:10240',
            'sample_video'        => 'nullable|mimes:mp4,mov,avi|max:51200',
        ]);

        if (isset($validated['languages']) && is_string($validated['languages'])) {
            $validated['languages'] = array_map('trim', explode(',', $validated['languages']));
        }
        $validated['travel_available'] = $request->has('travel_available');

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('singers', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('singers/covers', 'public');
        }
        if ($request->hasFile('sample_audio')) {
            $validated['sample_audio'] = $request->file('sample_audio')->store('singers/audio', 'public');
        }
        if ($request->hasFile('sample_video')) {
            $validated['sample_video'] = $request->file('sample_video')->store('singers/video', 'public');
        }

        Singer::create($validated);

        return redirect()->route('admin.singers.index')->with('success', 'Singer created successfully.');
    }

    public function edit($id) {
        $singer = Singer::findOrFail($id);
        $users = User::where('role', 'user')->get();
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
            'languages'           => 'nullable|string',
            'travel_available'    => 'boolean',
            'instagram_link'      => 'nullable|url',
            'youtube_link'        => 'nullable|url',
            'spotify_link'        => 'nullable|url',
            'cover_image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sample_audio'        => 'nullable|mimes:mp3,wav|max:10240',
            'sample_video'        => 'nullable|mimes:mp4,mov,avi|max:51200',
        ]);

        if (isset($validated['languages']) && is_string($validated['languages'])) {
            $validated['languages'] = array_map('trim', explode(',', $validated['languages']));
        }
        $validated['travel_available'] = $request->has('travel_available');

        if ($request->hasFile('profile_image')) {
            if ($singer->profile_image) {
                Storage::disk('public')->delete($singer->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('singers', 'public');
        }
        if ($request->hasFile('cover_image')) {
            if ($singer->cover_image) Storage::disk('public')->delete($singer->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('singers/covers', 'public');
        }
        if ($request->hasFile('sample_audio')) {
            if ($singer->sample_audio) Storage::disk('public')->delete($singer->sample_audio);
            $validated['sample_audio'] = $request->file('sample_audio')->store('singers/audio', 'public');
        }
        if ($request->hasFile('sample_video')) {
            if ($singer->sample_video) Storage::disk('public')->delete($singer->sample_video);
            $validated['sample_video'] = $request->file('sample_video')->store('singers/video', 'public');
        }

        $singer->update($validated);

        return redirect()->route('admin.singers.index')->with('success', 'Singer updated successfully.');
    }

    public function destroy($id) {
        $singer = Singer::findOrFail($id);
        
        if ($singer->profile_image) Storage::disk('public')->delete($singer->profile_image);
        if ($singer->cover_image) Storage::disk('public')->delete($singer->cover_image);
        if ($singer->sample_audio) Storage::disk('public')->delete($singer->sample_audio);
        if ($singer->sample_video) Storage::disk('public')->delete($singer->sample_video);

        $singer->delete();

        return redirect()->route('admin.singers.index')->with('success', 'Singer deleted successfully.');
    }
}