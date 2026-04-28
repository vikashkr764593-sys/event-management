<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller {
    public function index() {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }
    public function create() { return view('admin.users.create'); }
    public function store(Request $request) { return redirect()->route('admin.users.index')->with('success', 'Created successfully.'); }
    public function edit($id) {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    public function update(Request $request, $id) { return redirect()->route('admin.users.index')->with('success', 'Updated successfully.'); }
    public function destroy($id) {
        User::destroy($id);
        return redirect()->route('admin.users.index')->with('success', 'Deleted successfully.');
    }
}