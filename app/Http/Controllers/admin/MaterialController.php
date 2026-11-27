<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display the material master listing view.
     */
    public function material_list(Request $request)
    {
        return view('material.materiallist');
    }
}
