<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstrumentRequest;
use App\Http\Requests\UpdateInstrumentRequest;
use App\Http\Resources\InstrumentResource;
use App\Repositories\Contracts\InstrumentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Exception;

class InstrumentController extends Controller
{
    protected $instrumentRepo;

    public function __construct(InstrumentRepositoryInterface $instrumentRepo)
    {
        $this->instrumentRepo = $instrumentRepo;
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => InstrumentResource::collection($this->instrumentRepo->all())
        ]);
    }

    public function store(StoreInstrumentRequest $request): JsonResponse
    {
        try {
            $instrument = $this->instrumentRepo->create($request->validated());
            return response()->json([
                'status' => true,
                'data'   => new InstrumentResource($instrument)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $instrument = $this->instrumentRepo->find($id);
            return response()->json([
                'status' => true,
                'data'   => new InstrumentResource($instrument)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Instrument not found'], 404);
        }
    }

    public function update(UpdateInstrumentRequest $request, $id): JsonResponse
    {
        try {
            $instrument = $this->instrumentRepo->update($id, $request->validated());
            return response()->json([
                'status' => true,
                'data'   => new InstrumentResource($instrument)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->instrumentRepo->delete($id);
            return response()->json(['status' => true, 'message' => 'Instrument deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}