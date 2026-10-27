<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AutoCarCategory;

class AutoCarCategoryController extends Controller
{
    public function autoCategoryList(Request $request)
    {
        return view('autocar_category.list');
    }

    public function addAutoCategory(Request $request)
    {
        return view('autocar_category.add');
    }

    public function editAutoCategory(Request $request, $id)
    {
        $autoCarCategory = AutoCarCategory::findOrFail($id);
        return view('autocar_category.edit', compact('autoCarCategory'));
    }
}
