<?php

namespace App\Http\Controllers;

use App\Models\CltLayer;
use Illuminate\Http\Request;

class LayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layers = CltLayer::latest()->get();
        return response()->json($layers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'layup_id' => 'required|exists:clt_layups,id',
            'layer_order' => 'required|integer',
            'thickness' => 'required|decimal:0,2',
            'width' => 'required|decimal:0,2',
            'angle' => 'required|decimal:0,2',
        ]);
        $layer = CltLayer::create([
            'layup_id' => $request->layup_id,
            'layer_order' => $request->layer_order,
            'thickness' => $request->thickness,
            'width' => $request->width,
            'angle' => $request->angle
        ]);

        return response()->json([
            'message' => 'Layer created',
            'data' => $layer
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $layer = CltLayer::with('layers.layers')->findOrFail($id);
        return response()->json($layer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'layer_order' => 'required|integer',
            'thickness' => 'required|decimal:0,2',
            'width' => 'required|decimal:0,2',
            'angle' => 'required|decimal:0,2',
        ]);
        $layer = CltLayer::findOrFail($id);
        $layer->update([
            'layer_order' => $request->layer_order,
            'thickness' => $request->thickness,
            'width' => $request->width,
            'angle' => $request->angle
        ]);

        return response()->json([
            'message' => 'Layer updated',
            'data' => $layer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $layer = CltLayer::findOrFail($id);
        $layer->delete();

        return response()->json([
            'message' => 'Layer deleted'
        ]);
    }
}
