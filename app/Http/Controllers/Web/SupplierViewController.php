<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierViewController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('layups')->get();
        return view('suppliers.index', compact('suppliers'));
    }
}
