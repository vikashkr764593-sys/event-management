<?php
$dir = __DIR__ . '/app/Http/Controllers/Admin/';
if (!is_dir($dir)) mkdir($dir, 0755, true);

$controllers = [
    'AdminUserController' => 'User',
    'AdminSingerController' => 'Singer',
    'AdminInstrumentController' => 'Instrument',
    'AdminBookingController' => 'Booking',
    'AdminOrderController' => 'Order'
];

foreach ($controllers as $className => $model) {
    $plural = strtolower($model) . 's';
    if ($model == 'User') $plural = 'users';
    $content = "<?php\nnamespace App\Http\Controllers\Admin;\nuse App\Http\Controllers\Controller;\nuse App\Models\\$model;\nuse Illuminate\Http\Request;\n\nclass $className extends Controller {\n    public function index() {\n        \$$plural = $model::all();\n        return view('admin.$plural.index', compact('$plural'));\n    }\n    public function create() { return view('admin.$plural.create'); }\n    public function store(Request \$request) { return redirect()->route('admin.$plural.index')->with('success', 'Created successfully.'); }\n    public function edit(\$id) {\n        \$$strtolower = $model::findOrFail(\$id);\n        return view('admin.$plural.edit', compact('$strtolower'));\n    }\n    public function update(Request \$request, \$id) { return redirect()->route('admin.$plural.index')->with('success', 'Updated successfully.'); }\n    public function destroy(\$id) {\n        $model::destroy(\$id);\n        return redirect()->route('admin.$plural.index')->with('success', 'Deleted successfully.');\n    }\n}";
    
    // Quick fix for the $strtolower variable that was mistakenly written
    $varName = strtolower($model);
    $content = str_replace('$strtolower', '$' . $varName, $content);
    
    file_put_contents($dir . $className . '.php', $content);
}

echo "Admin controllers created.\n";
