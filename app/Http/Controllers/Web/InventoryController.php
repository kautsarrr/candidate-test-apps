<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\CltLayup;

class InventoryController extends Controller
{
    /**
     * layups & layers by supplier
     */
    public function layup($id)
    {
        $supplier = Supplier::with('layups.layers')->findOrFail($id);
        return view('inventory.layup', [
            'supplier' => $supplier,
            'layups' => $supplier->layups
        ]);
    }

    /**
     * Detail layup + layers
     */
    public function layer($id)
    {
        $layup = CltLayup::with('layers')->findOrFail($id);
        return view('inventory.layer', [
            'layup' => $layup,
            'layers' => $layup->layers
        ]);
    }
}
