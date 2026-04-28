<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Exception;

class ChatController extends Controller
{
    /**
     * Send a new chat message for a booking.
     *
     * @param SendChatRequest $request
     * @return JsonResponse
     */
    public function send(SendChatRequest $request): JsonResponse
    {
        try {
            $chat = Chat::create([
                'booking_id' => $request->booking_id, 
                'sender_id'  => $request->user()->id, 
                'message'    => $request->message
            ]);

            return response()->json([
                'status' => true, 
                'data'   => new ChatResource($chat->load('sender'))
            ], 201);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
    
    /**
     * Get all chat messages for a specific booking.
     *
     * @param int $booking_id
     * @return JsonResponse
     */
    public function getByBooking($booking_id): JsonResponse
    {
        try {
            $chats = Chat::where('booking_id', $booking_id)
                         ->with('sender')
                         ->orderBy('created_at', 'asc')
                         ->get();

            return response()->json([
                'status' => true, 
                'data'   => ChatResource::collection($chats)
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
}