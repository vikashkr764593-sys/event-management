<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Singer;
use Illuminate\Http\Request;

class AdminSingerController extends Controller {
    public function index() {
        $singers = Singer::all();
        return view('admin.singers.index', compact('singers'));
    }
    public function create() { return view('admin.singers.create'); }
    public function store(Request $request) { return redirect()->route('admin.singers.index')->with('success', 'Created successfully.'); }
    public function edit($id) {
        $singer = Singer::findOrFail($id);
        return view('admin.singers.edit', compact('singer'));
    }
    public function update(Request $request, $id) { return redirect()->route('admin.singers.index')->with('success', 'Updated successfully.'); }
    public function destroy($id) {
        Singer::destroy($id);
        return redirect()->route('admin.singers.index')->with('success', 'Deleted successfully.');
    }
}