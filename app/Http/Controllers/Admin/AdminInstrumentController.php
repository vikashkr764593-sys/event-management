<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Instrument;
use Illuminate\Http\Request;

class AdminInstrumentController extends Controller {
    public function index() {
        $instruments = Instrument::all();
        return view('admin.instruments.index', compact('instruments'));
    }
    public function create() { return view('admin.instruments.create'); }
    public function store(Request $request) { return redirect()->route('admin.instruments.index')->with('success', 'Created successfully.'); }
    public function edit($id) {
        $instrument = Instrument::findOrFail($id);
        return view('admin.instruments.edit', compact('instrument'));
    }
    public function update(Request $request, $id) { return redirect()->route('admin.instruments.index')->with('success', 'Updated successfully.'); }
    public function destroy($id) {
        Instrument::destroy($id);
        return redirect()->route('admin.instruments.index')->with('success', 'Deleted successfully.');
    }
}