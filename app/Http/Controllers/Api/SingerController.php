<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSingerRequest;
use App\Http\Requests\UpdateSingerRequest;
use App\Http\Requests\StoreSingerAvailabilityRequest;
use App\Http\Requests\UpdateSingerAvailabilityRequest;
use App\Http\Resources\SingerResource;
use App\Http\Resources\SingerAvailabilityResource;
use App\Models\Singer;
use App\Models\SingerAvailability;
use App\Repositories\Contracts\SingerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class SingerController extends Controller
{
    protected $singerRepo;

    public function __construct(SingerRepositoryInterface $singerRepo)
    {
        $this->singerRepo = $singerRepo;
        // Middleware removed to align with simplified Role split (Admin=Web)
    }

    /**
     * Display a listing of singers.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => SingerResource::collection($this->singerRepo->all())
        ]);
    }

    /**
     * Display the specified singer.
     */
    public function show($id): JsonResponse
    {
        try {
            $singer = $this->singerRepo->find($id);
            return response()->json([
                'status' => true,
                'data'   => new SingerResource($singer)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Singer not found'], 404);
        }
    }

    /**
     * Get availability dates for a specific singer.
     */
    public function getAvailability($id): JsonResponse
    {
        try {
            // Verify singer exists via repo
            $this->singerRepo->find($id);

            $availabilities = SingerAvailability::where('singer_id', $id)
                ->orderBy('date', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => SingerAvailabilityResource::collection($availabilities)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Singer not found.'], 404);
        }
    }

    /**
     * Manage availability records. 
     * Note: Creating/Updating availability is currently permitted via API 
     * for singers to manage their own schedules.
     */
    public function storeAvailability(StoreSingerAvailabilityRequest $request, $id): JsonResponse
    {
        try {
            $singer = $this->singerRepo->find($id);

            // Authorization: Only the singer themselves or an admin can manage availability
            if (request()->user()->role !== 'admin' && request()->user()->id !== $singer->user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You can only manage your own availability.'
                ], 403);
            }

            $data = $request->validated();
            $data['singer_id'] = $id;

            $availability = SingerAvailability::create($data);

            return response()->json([
                'status' => true,
                'data'   => new SingerAvailabilityResource($availability)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateAvailability(UpdateSingerAvailabilityRequest $request, $id): JsonResponse
    {
        try {
            $availability = SingerAvailability::with('singer')->findOrFail($id);

            // Authorization Check
            if (request()->user()->role !== 'admin' && request()->user()->id !== $availability->singer->user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You can only manage your own availability.'
                ], 403);
            }

            $availability->update($request->validated());

            return response()->json([
                'status' => true,
                'data'   => new SingerAvailabilityResource($availability)
            ]);
        } catch (Exception $e) {
            $code = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json(['status' => false, 'message' => $e->getMessage()], $code);
        }
    }

    // Administrative methods (store, update, destroy) are removed from API 
    // to enforce the Web-only Admin Management architecture.

    /**
     * Update the logged-in singer's professional profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Get the singer profile for this user
            $singer = Singer::where('user_id', $user->id)->first();

            if (!$singer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Singer profile not found for this user.'
                ], 404);
            }

            $validated = $request->validate([
                'bio' => 'nullable|string',
                'languages' => 'nullable|array',
                'languages.*' => 'string',
                'experience' => 'nullable|integer',
                'travel_available' => 'nullable|boolean',
                'starting_price' => 'nullable|numeric',
                'instagram_link' => 'nullable|url',
                'youtube_link' => 'nullable|url',
                'spotify_link' => 'nullable|url',
                'cover_image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
                'sample_audio' => 'nullable|file|mimes:mp3,wav|max:10240',
                'sample_video' => 'nullable|file|mimes:mp4,mov,avi|max:51200',
            ]);

            // Handle file uploads
            if ($request->hasFile('cover_image')) {
                $singer->cover_image = $request->file('cover_image')->store('singers/covers', 'public');
            }
            if ($request->hasFile('sample_audio')) {
                $singer->sample_audio = $request->file('sample_audio')->store('singers/audio', 'public');
            }
            if ($request->hasFile('sample_video')) {
                $singer->sample_video = $request->file('sample_video')->store('singers/video', 'public');
            }

            // Map and update textual fields
            if (isset($validated['bio'])) $singer->biography = $validated['bio'];
            if (isset($validated['languages'])) $singer->languages = $validated['languages'];
            if (isset($validated['experience'])) $singer->experience_years = $validated['experience'];
            if (isset($validated['travel_available'])) $singer->travel_available = $validated['travel_available'];
            if (isset($validated['starting_price'])) $singer->fee = $validated['starting_price'];
            if (isset($validated['instagram_link'])) $singer->instagram_link = $validated['instagram_link'];
            if (isset($validated['youtube_link'])) $singer->youtube_link = $validated['youtube_link'];
            if (isset($validated['spotify_link'])) $singer->spotify_link = $validated['spotify_link'];
            
            $singer->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'data' => new SingerResource($singer)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
