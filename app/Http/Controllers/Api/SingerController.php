<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSingerRequest;
use App\Http\Requests\UpdateSingerRequest;
use App\Http\Resources\SingerResource;
use App\Repositories\Contracts\SingerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class SingerController extends Controller
{
    protected $singerRepo;

    public function __construct(SingerRepositoryInterface $singerRepo)
    {
        $this->singerRepo = $singerRepo;
    }

    public function index(): JsonResponse
    {
        // For actual implementation, the repository should load the 'user' relationship
        // e.g. return $this->singerRepo->with('user')->get(); 
        // We'll just return what's available for now
        return response()->json([
            'status' => true,
            'data'   => SingerResource::collection($this->singerRepo->all())
        ]);
    }

    public function store(StoreSingerRequest $request): JsonResponse
    {
        try {
            $singer = $this->singerRepo->create($request->validated());
            return response()->json([
                'status' => true,
                'data'   => new SingerResource($singer)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

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

    public function update(UpdateSingerRequest $request, $id): JsonResponse
    {
        try {
            $singer = $this->singerRepo->update($id, $request->validated());
            return response()->json([
                'status' => true,
                'data'   => new SingerResource($singer)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->singerRepo->delete($id);
            return response()->json(['status' => true, 'message' => 'Singer deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get availability dates for a specific singer.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getAvailability($id): JsonResponse
    {
        try {
            // Verify singer exists
            $singer = $this->singerRepo->find($id);
            
            $availabilities = \App\Models\SingerAvailability::where('singer_id', $id)
                ->orderBy('date', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => \App\Http\Resources\SingerAvailabilityResource::collection($availabilities)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Singer not found.'], 404);
        }
    }

    /**
     * Add a new availability record for a singer.
     *
     * @param \App\Http\Requests\StoreSingerAvailabilityRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function storeAvailability(\App\Http\Requests\StoreSingerAvailabilityRequest $request, $id): JsonResponse
    {
        try {
            // Verify singer exists
            $this->singerRepo->find($id);

            $data = $request->validated();
            $data['singer_id'] = $id;

            $availability = \App\Models\SingerAvailability::create($data);

            return response()->json([
                'status' => true,
                'data'   => new \App\Http\Resources\SingerAvailabilityResource($availability)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing availability record.
     * Note: the $id parameter here represents the availability record ID, not the singer ID, 
     * based on standard REST practices for an update endpoint.
     *
     * @param \App\Http\Requests\UpdateSingerAvailabilityRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateAvailability(\App\Http\Requests\UpdateSingerAvailabilityRequest $request, $id): JsonResponse
    {
        try {
            $availability = \App\Models\SingerAvailability::findOrFail($id);
            $availability->update($request->validated());

            return response()->json([
                'status' => true,
                'data'   => new \App\Http\Resources\SingerAvailabilityResource($availability)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Availability record not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}