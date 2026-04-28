<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller {
    public function index() {
        $bookings = Booking::all();
        return view('admin.bookings.index', compact('bookings'));
    }
    public function create() { return view('admin.bookings.create'); }
    public function store(Request $request) { return redirect()->route('admin.bookings.index')->with('success', 'Created successfully.'); }
    public function edit($id) {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.edit', compact('booking'));
    }
    public function update(Request $request, $id) { return redirect()->route('admin.bookings.index')->with('success', 'Updated successfully.'); }
    public function destroy($id) {
        Booking::destroy($id);
        return redirect()->route('admin.bookings.index')->with('success', 'Deleted successfully.');
    }
}