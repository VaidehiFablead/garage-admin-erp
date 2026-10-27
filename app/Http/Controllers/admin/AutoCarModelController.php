<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AutoCarModel;

class AutoCarModelController extends Controller
{
    public function modelList(Request $request)
    {
        return view('autocar_model.list');
    }

    public function addModel(Request $request)
    {
        return view('autocar_model.add');
    }

    public function editModel(Request $request, $id)
    {
        $autoCarModel = AutoCarModel::findOrFail($id);
        return view('autocar_model.edit', compact('autoCarModel'));
    }
}
