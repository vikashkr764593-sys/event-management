<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminInstrumentController extends Controller {
    
    public function index() {
        $instruments = Instrument::with('category')->latest()->get();
        return view('admin.instruments.index', compact('instruments'));
    }

    public function create() { 
        $categories = Category::all();
        return view('admin.instruments.create', compact('categories')); 
    }

    public function store(Request $request) { 
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock'    => 'required|integer|min:0',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('instruments', 'public');
        }

        Instrument::create($validated);

        return redirect()->route('admin.instruments.index')->with('success', 'Instrument created successfully.');
    }

    public function edit($id) {
        $instrument = Instrument::findOrFail($id);
        $categories = Category::all();
        return view('admin.instruments.edit', compact('instrument', 'categories'));
    }

    public function update(Request $request, $id) { 
        $instrument = Instrument::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock'    => 'required|integer|min:0',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($instrument->image && Storage::disk('public')->exists($instrument->image)) {
                Storage::disk('public')->delete($instrument->image);
            }
            $validated['image'] = $request->file('image')->store('instruments', 'public');
        }

        $instrument->update($validated);

        return redirect()->route('admin.instruments.index')->with('success', 'Instrument updated successfully.');
    }

    public function destroy($id) {
        $instrument = Instrument::findOrFail($id);

        if ($instrument->image && Storage::disk('public')->exists($instrument->image)) {
            Storage::disk('public')->delete($instrument->image);
        }

        $instrument->delete();
        
        return redirect()->route('admin.instruments.index')->with('success', 'Instrument deleted successfully.');
    }
}