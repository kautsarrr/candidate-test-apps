<?php

namespace App\Http\Controllers;

use App\Models\CltLayup;
use Illuminate\Http\Request;

class LayupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layups = CltLayup::latest()->get();
        return response()->json($layups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'name' => 'required|string|max:255'
        ]);
        $layup = CltLayup::create([
            'supplier_id' => $request->supplier_id,
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Layup created',
            'data' => $layup
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $layup = CltLayup::with('layups.layers')->findOrFail($id);
        return response()->json($layup);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $layup = CltLayup::findOrFail($id);
        $layup->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Layup updated',
            'data' => $layup
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $layup = CltLayup::findOrFail($id);
        $layup->delete();

        return response()->json([
            'message' => 'Layup deleted'
        ]);
    }
}
