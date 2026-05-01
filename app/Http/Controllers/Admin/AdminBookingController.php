<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Singer;
use Illuminate\Http\Request;

class AdminBookingController extends Controller {
    
    public function index() {
        $bookings = Booking::with(['user', 'singer'])->latest()->get();
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create() { 
        $users = User::all();
        $singers = Singer::all();
        return view('admin.bookings.create', compact('users', 'singers')); 
    }

    public function store(Request $request) { 
        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'singer_id'  => 'required|exists:singers,id',
            'event_date' => 'required|date|after_or_equal:today',
            'time_slot'  => 'required|date_format:H:i',
            'event_type' => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
            'status'     => 'required|in:pending,approved,rejected,cancelled',
        ]);

        // Check for conflicts
        $conflict = Booking::where('singer_id', $validated['singer_id'])
                           ->where('event_date', $validated['event_date'])
                           ->where('time_slot', $validated['time_slot'])
                           ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'Booking Conflict: This singer is already booked for the selected date and time.');
        }

        Booking::create($validated);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    public function edit($id) {
        $booking = Booking::findOrFail($id);
        $users = User::all();
        $singers = Singer::all();
        return view('admin.bookings.edit', compact('booking', 'users', 'singers'));
    }

    public function update(Request $request, $id) { 
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'singer_id'  => 'required|exists:singers,id',
            'event_date' => 'required|date',
            'time_slot'  => 'required|date_format:H:i',
            'event_type' => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
            'status'     => 'required|in:pending,approved,rejected,cancelled',
        ]);

        // If the date is changing, ensure it's not in the past
        if ($validated['event_date'] !== $booking->event_date->format('Y-m-d') && $validated['event_date'] < now()->format('Y-m-d')) {
            return back()->withInput()->with('error', 'Event date cannot be in the past.');
        }

        // Check for conflicts
        $conflict = Booking::where('singer_id', $validated['singer_id'])
                           ->where('event_date', $validated['event_date'])
                           ->where('time_slot', $validated['time_slot'])
                           ->where('id', '!=', $id) // Ignore current booking
                           ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'Booking Conflict: This singer is already booked for the selected date and time.');
        }

        $booking->update($validated);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy($id) {
        Booking::destroy($id);
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }
}