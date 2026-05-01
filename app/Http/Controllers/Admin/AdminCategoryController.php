<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller {
    
    public function index() {
        $categories = Category::withCount('instruments')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create() { 
        return view('admin.categories.create'); 
    }

    public function store(Request $request) { 
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id) {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id) { 
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id) {
        $category = Category::findOrFail($id);

        if ($category->instruments()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category because it has instruments linked to it.');
        }

        $category->delete();
        
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
