<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Import base Controller class
use App\Models\LabourService;

class LabourServiceController extends Controller
{
    public function labour_list(Request $request)
    {
        return view('labour_service/labour_list');
    }
    public function add_labour(Request $request)
    {
        return view('labour_service/add_service');
    }
    public function edit_labour(Request $request, $id)
    {
        $labourService = LabourService::findOrFail($id);

        return view('labour_service/edit_service', compact('labourService'));
    }
}
