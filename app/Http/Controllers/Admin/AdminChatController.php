<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Booking;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    /**
     * Display a listing of all chat threads.
     */
    public function index()
    {
        // Get unique booking_ids and order_ids from chats
        $bookingChats = Chat::whereNotNull('booking_id')
            ->with(['booking.user', 'sender'])
            ->get()
            ->groupBy('booking_id');

        $orderChats = Chat::whereNotNull('order_id')
            ->with(['order.user', 'sender'])
            ->get()
            ->groupBy('order_id');

        return view('admin.chats.index', compact('bookingChats', 'orderChats'));
    }

    /**
     * Show the chat history for a specific context.
     */
    public function show(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $orderId = $request->query('order_id');

        $query = Chat::with('sender')->orderBy('created_at', 'asc');

        if ($bookingId) {
            $chats = $query->where('booking_id', $bookingId)->get();
            $context = Booking::with('user')->findOrFail($bookingId);
            $type = 'Booking';
        } elseif ($orderId) {
            $chats = $query->where('order_id', $orderId)->get();
            $context = Order::with('user')->findOrFail($orderId);
            $type = 'Order';
        } else {
            return redirect()->route('admin.chats.index');
        }

        return view('admin.chats.show', compact('chats', 'context', 'type'));
    }

    /**
     * Store an admin reply.
     */
    public function reply(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'order_id'   => 'nullable|exists:orders,id',
            'message'    => 'required|string|max:2000',
        ]);

        Chat::create([
            'booking_id' => $validated['booking_id'] ?? null,
            'order_id'   => $validated['order_id'] ?? null,
            'sender_id'  => Auth::id(),
            'message'    => $validated['message'],
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }
}
