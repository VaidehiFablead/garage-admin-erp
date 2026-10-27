<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
         $key = 'login-attempts-' . $request->ip();
        $force = filter_var($request->input('force', false), FILTER_VALIDATE_BOOLEAN);
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => false,
                'error' => 'Too many login attempts. Please try again after 10 minutes.'
            ], 429);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($key); // reset after success    
            $user = Auth::user();
            $token = $user->createToken('LaravelPassportToken')->accessToken;

            // Decide redirect based on role
            $redirect = route('auth.dashboard'); // default
            if (strtolower($user->role) === 'staff') {
                $redirect = route('auth.staff-dashboard');
            }
            // dd($user);

            return response()->json([
                'status' => true,
                'token' => $token,
                'redirect' => $redirect,
                'showAppointments' => ($user->role !== 'staff'), // example: only non-staff see appointments
            ], 200);
        } else {
            RateLimiter::hit($key, 600);
            return response()->json(['status' => false, 'error' => 'Login Credential Wrong'], 401);
        }
    }

    // Logout User
    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); // Logout user
        $request->session()->invalidate(); // Invalidate the session
        $request->session()->regenerateToken(); // Regenerate CSRF token

        return redirect('/');
    }

    public function logoutapi(Request $request)
    {
        $request->user()->token()->revoke(); // Revoke access token
        return response()->json(['status' => true, 'message' => 'Logout Successfully'], 200);
    }
}
