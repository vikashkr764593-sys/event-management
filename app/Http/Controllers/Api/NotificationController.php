<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $notifications = $request->user()->notifications;

            return response()->json([
                'status' => true, 
                'data'   => $notifications
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
    
    /**
     * Mark a specific notification as read.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        try {
            $notification = $request->user()->notifications()->findOrFail($id);
            $notification->markAsRead();

            return response()->json([
                'status' => true, 
                'message' => 'Notification marked as read'
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
}