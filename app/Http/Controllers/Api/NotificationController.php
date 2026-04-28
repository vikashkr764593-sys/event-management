<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Exception;

class NotificationController extends Controller
{
    /**
     * Fetch all notifications for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $notifications = Notification::where('user_id', $request->user()->id)
                                         ->orderBy('id', 'desc')
                                         ->get();

            return response()->json([
                'status' => true, 
                'data'   => NotificationResource::collection($notifications)
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
    
    /**
     * Mark a specific notification as read.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function markAsRead($id): JsonResponse
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->update(['is_read' => true]);

            return response()->json([
                'status' => true, 
                'data'   => new NotificationResource($notification)
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
}