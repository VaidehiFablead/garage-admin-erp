<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display the type master listing view.
     */
    public function type_list(Request $request)
    {
        return view('type.typelist');
    }
}
