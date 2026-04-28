<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => UserResource::collection($this->userRepo->all())
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            
            $user = $this->userRepo->create($data);
            
            return response()->json([
                'status' => true,
                'data'   => new UserResource($user)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $user = $this->userRepo->find($id);
            return response()->json([
                'status' => true,
                'data'   => new UserResource($user)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
    }

    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        try {
            $data = $request->validated();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $user = $this->userRepo->update($id, $data);
            return response()->json([
                'status' => true,
                'data'   => new UserResource($user)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->userRepo->delete($id);
            return response()->json(['status' => true, 'message' => 'User deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}