<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Instrument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller {
    
    public function index() {
        $orders = Order::with(['user'])->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function create() { 
        $users = User::all();
        $instruments = Instrument::where('stock', '>', 0)->get();
        return view('admin.orders.create', compact('users', 'instruments')); 
    }

    public function store(Request $request) { 
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'instrument_id' => 'required|array',
            'instrument_id.*' => 'exists:instruments,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
            'status' => 'required|in:requested,approved,issued',
            'payment_status' => 'required|in:Pending,Paid,Failed',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'total_amount' => 0, // Will be calculated below
            ]);

            $totalAmount = 0;

            foreach ($validated['instrument_id'] as $index => $instrumentId) {
                $quantity = $validated['quantity'][$index];
                $instrument = Instrument::findOrFail($instrumentId);
                
                // Snap price at the moment of order
                $price = $instrument->price;
                $lineTotal = $price * $quantity;
                $totalAmount += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'instrument_id' => $instrumentId,
                    'quantity' => $quantity,
                    'price' => $price, // Snapshot price!
                ]);

                // Immediately sync inventory if created as Paid
                if ($validated['payment_status'] === 'Paid') {
                    if ($instrument->stock >= $quantity) {
                        $instrument->decrement('stock', $quantity);
                    } else {
                        throw new \Exception("Insufficient stock for {$instrument->name}");
                    }
                }
            }

            // Update order with actual calculated total
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order created successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show($id) {
        // Eager load everything needed for the Order Summary
        $order = Order::with(['user', 'items.instrument'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function edit($id) {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, $id) { 
        $order = Order::with('items.instrument')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:requested,approved,issued',
            'payment_status' => 'required|in:Pending,Paid,Failed',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Check if payment status is transitioning to Paid for the first time
            if ($order->payment_status !== 'Paid' && $validated['payment_status'] === 'Paid') {
                // Inventory Sync: Decrement stock for all items
                foreach ($order->items as $item) {
                    $instrument = $item->instrument;
                    if ($instrument && $instrument->stock >= $item->quantity) {
                        $instrument->decrement('stock', $item->quantity);
                    } else {
                        throw new \Exception("Insufficient stock for instrument ID {$item->instrument_id}");
                    }
                }
            }

            $order->update($validated);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order updated successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        Order::destroy($id); // OrderItems will cascade delete via DB constraints if configured, otherwise we should delete items first. Schema says cascadeOnDelete.
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}