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
        $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized - No user found'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image',
            'password' => 'nullable|string|min:6|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $oldEmail = $user->email;
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('email')) $user->email = $request->email;

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // If updating admin user profile, also synchronize changes to master admin_erp
        if ($user->role === 'admin' || (int) ($user->role_id ?? 0) === 1 || (int) $user->id === 1) {
            try {
                $tenantManager = app(\App\Services\Tenancy\TenantManager::class);
                if ($tenantManager->enabled()) {
                    $tenant = $tenantManager->current();
                    $tenantManager->syncProfileToAdmin([
                        'client_id' => $tenant['client_id'] ?? null,
                        'tenant_id' => $tenant['id'] ?? null,
                        'domain' => request()->getHost(),
                        'current_email' => $oldEmail,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'password' => $request->filled('password') ? $request->password : null,
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Profile update sync to admin_erp failed: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => true, 'message' => 'Profile updated successfully'], 200);
    }

    public function get_subadmin(Request $request)
    {
        $user = null;
        try {
            $user = Auth::guard('api')->user();
        } catch (\Throwable $e) {
            $user = null;
        }

        if (!$user) {
            try {
                $user = Auth::guard('web')->user();
            } catch (\Throwable $e) {
                $user = null;
            }
        }

        $role = $user->role ?? '';

        if (!$user || $role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized - Only admin allowed'], 403);
        }

        try {
            $subadmin = User::where('role', 'sub-admin')
                ->where('isDeleted', 0)
                ->select('id', 'name', 'profile_image')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $subadmin,
                'role' => $role
            ], 200);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

