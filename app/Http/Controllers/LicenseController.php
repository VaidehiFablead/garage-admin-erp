<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class LicenseController extends Controller
{
    public function showForm()
    {
        return view('setup.license');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'purchase_code' => 'required'
        ]);

        $baseUrl = rtrim(env('LICENSE_SERVER_URL', 'https://your-central-server.com'), '/');
        
        try {
            $response = Http::post($baseUrl . '/api/verify-license', [
                'username' => $request->username,
                'purchase_code' => $request->purchase_code,
                'domain' => request()->getHost(), // Send the domain where it's being installed
                'product_code' => env('LICENSE_PRODUCT_CODE'),
            ]);
            $isSuccessful = $response->successful() && $response->json('status') === 'valid';
        } catch (\Exception $e) {
            $isSuccessful = false;
        }

        // 2. Check the response from your server or bypass if in local mode
        if ($isSuccessful || env('LICENSE_MODE') === 'local') {
            
            // 3. If valid, save a file to indicate the software is activated
            // We use a simple local file, but you could also save this to a database
            File::put(storage_path('app/license.key'), json_encode([
                'activated_at' => now(),
                'purchase_code' => $request->purchase_code
            ]));

            return redirect()->route('login')->with('success', 'Software activated successfully!');
        }

        // If invalid, redirect back with an error
        return back()->with('error', 'Invalid Purchase Code or Username.');
    }
}
