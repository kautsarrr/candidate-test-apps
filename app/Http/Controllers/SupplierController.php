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

    public function import(Request $request, $id, ImportService $importService)
    {
        $request->validate([
            'data' => 'required|array'
        ]);

        $importService->import(
            $id,
            $request->data,
            $request->strategy ?? 'overwrite'
        );

        return response()->json([
            'message' => 'Import success'
        ]);
    }
}
