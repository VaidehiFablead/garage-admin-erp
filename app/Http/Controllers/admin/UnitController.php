<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitController extends Controller
{
   public function unit_list(Request $request)
    {
        return view('unit/unitlist');
    }
}
