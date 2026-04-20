<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\ImportService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        return response()->json($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $supplier = Supplier::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Supplier created',
            'data' => $supplier
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::with('layups.layers')->findOrFail($id);
        return response()->json($supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Supplier updated',
            'data' => $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted'
        ]);
    }

    public function export($id)
    {
        $supplier = Supplier::with('layups.layers')->findOrFail($id);

        return response()->json([
            'data' => $supplier
        ]);
    }

    public function exportAll()
    {
        $suppliers = Supplier::with('layups.layers')->get();

        return response()->json([
            'data' => $suppliers
        ]);
    }

public function import(Request $request, ImportService $importService)
{
    if ($request->hasFile('file')) {
        $content = file_get_contents($request->file('file')->getRealPath());
        $json = json_decode($content, true);

        if (!$json) {
            return response()->json([
                'message' => 'File JSON tidak valid'
            ], 422);
        }

        $data = isset($json[0]) ? $json : [$json];
    } else {
        $data = $request->input('data');

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 422);
        }
    }

    foreach ($data as $item) {
        if (!isset($item['name'])) {
            return response()->json([
                'message' => 'Semua supplier harus punya name'
            ], 422);
        }
    }

    $importService->import($data);

    return response()->json([
        'message' => 'Import success'
    ]);
}
}
