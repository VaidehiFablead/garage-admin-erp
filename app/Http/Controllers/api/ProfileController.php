<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function getProfile()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized - No user found'], 401);
        }

        return response()->json(['status' => true, 'data' => $user], 200);
    }


    public function updateProfile(Request $request)
    {
        $user = Auth::guard('api')->user();
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            ],
        ],[
            // ✅ CUSTOM ERROR MESSAGE HERE
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&).',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email; // updated email

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['status' => true, 'message' => 'Profile updated successfully'], 200);
    }

    public function get_subadmin(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = $user->role ?? '';

        // dd($user, $role);

        if (!$user || $role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized - Only admin allowed'], 403);
        }

        $subadmin = User::where('role', 'sub-admin')
            ->where('isDeleted', 0)
            ->select('id', 'name', 'profile_image')
            ->get();

        // dd($subadmin);

        if ($subadmin->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No subadmin found'], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $subadmin,
            'role' => $role
        ], 200);
    }
}
