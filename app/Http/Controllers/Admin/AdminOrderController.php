<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller {
    public function index() {
        $orders = Order::all();
        return view('admin.orders.index', compact('orders'));
    }
    public function create() { return view('admin.orders.create'); }
    public function store(Request $request) { return redirect()->route('admin.orders.index')->with('success', 'Created successfully.'); }
    public function edit($id) {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }
    public function update(Request $request, $id) { return redirect()->route('admin.orders.index')->with('success', 'Updated successfully.'); }
    public function destroy($id) {
        Order::destroy($id);
        return redirect()->route('admin.orders.index')->with('success', 'Deleted successfully.');
    }
}