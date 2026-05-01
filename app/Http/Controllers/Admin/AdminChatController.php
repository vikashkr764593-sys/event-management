<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminChatController extends Controller
{
    /**
     * Display the chat interface with all conversations.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch all conversations the user is participating in
        $conversations = $user->conversations()
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1);
            }, 'participants'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('admin.chat.index', compact('conversations'));
    }

    /**
     * Fetch message history for a specific conversation.
     */
    public function show($id)
    {
        $conversation = Conversation::with(['messages.sender', 'participants'])
            ->findOrFail($id);

        // Ensure the current user is a participant
        if (!$conversation->participants->contains(Auth::id())) {
            abort(403);
        }

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'conversation' => $conversation,
            'messages' => $conversation->messages
        ]);
    }

    /**
     * Send a new message in a conversation.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'message_text' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($id);

        // Ensure the current user is a participant
        if (!$conversation->participants->contains(Auth::id())) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'message_text' => $request->message_text,
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            DB::commit();

            return response()->json($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Start a new conversation with a user.
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $recipientId = $request->user_id;
        $authId = Auth::id();

        // Check if a conversation already exists between these two users (assuming 1-on-1 for now)
        $conversation = Conversation::whereHas('participants', function ($query) use ($authId) {
            $query->where('user_id', $authId);
        })->whereHas('participants', function ($query) use ($recipientId) {
            $query->where('user_id', $recipientId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create();
            $conversation->participants()->attach([$authId, $recipientId]);
        }

        return redirect()->route('admin.chat.index', ['conversation_id' => $conversation->id]);
    }
}
