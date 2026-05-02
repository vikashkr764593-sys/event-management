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
}
