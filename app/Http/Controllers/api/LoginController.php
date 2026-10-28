<?php

namespace App\Http\Controllers\api;

use App\Exceptions\TenantResolutionException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'nullable|string',
            'email'    => 'nullable|string',
            'password' => 'required|string',
        ]);

        $loginIdentifier = trim((string)($request->input('username') ?? $request->input('email') ?? ''));
        if ($loginIdentifier === '') {
            return response()->json([
                'status' => false,
                'error'  => 'Username or Email is required.'
            ], 422);
        }

        // 1. Re-check the tenant with admin-products if tenancy is enabled
        if ($tenantDeny = $this->ensureTenantAllowsLogin($request)) {
            return $tenantDeny;
        }

        $key = 'login-attempts-' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => false,
                'error' => 'Too many login attempts. Please try again after 10 minutes.'
            ], 429);
        }

        $authResult = $this->authenticateByEmailPassword(
            $loginIdentifier,
            $request->password
        );

        if ($authResult['error']) {
            RateLimiter::hit($key, 600);
            $statusCode = $authResult['error'] === 'Wrong Login credentials entered.' ? 401 : 403;

            return response()->json([
                'status' => false,
                'error'  => $authResult['error'],
            ], $statusCode);
        }

        RateLimiter::clear($key); // reset after success    
        $user = $authResult['user'];

        if ($request->hasSession()) {
            $tenancy = app(TenantManager::class);
            if ($tenancy->enabled() && $tenancy->current()) {
                $request->session()->put('tenant_id', $tenancy->current()['id']);
                $request->session()->put('tenant', $tenancy->current());
            }
            Auth::login($user);
            $request->session()->put('showTodayAlertsModal', $user->role === 'admin');

            // If sub-admin or staff with branch_id, initialize selectedSubAdminId
            if ($user->role === 'sub-admin') {
                $request->session()->put('selectedSubAdminId', $user->id);
            } elseif ($user->role === 'staff' && !empty($user->branch_id)) {
                $request->session()->put('selectedSubAdminId', $user->branch_id);
            }
            $request->session()->save();
        }

        // Create personal access token for Passport
        $token = null;
        try {
            $token = $user->createToken('LaravelPassportToken')->accessToken;
        } catch (\Throwable) {
            // Token generation fallback
        }

        // Decide redirect based on role
        $redirect = route('auth.dashboard'); // default
        if (strtolower($user->role) === 'staff') {
            $redirect = route('auth.staff-dashboard');
        }

        return response()->json([
            'status' => true,
            'token' => $token,
            'user' => $user,
            'redirect' => $redirect,
            'showAppointments' => ($user->role !== 'staff'),
        ], 200);
    }

    /**
     * Authenticate by username/email/password, preferring the newest active account when
     * duplicate records exist (e.g. after a soft-deleted staff record).
     */
    protected function authenticateByEmailPassword(string $login, string $password): array
    {
        $cleanLogin = strtolower(trim($login));
        $candidates = User::where(function ($q) use ($cleanLogin) {
            if (Schema::hasColumn('users', 'username')) {
                $q->whereRaw('LOWER(TRIM(username)) = ?', [$cleanLogin])
                  ->orWhereRaw('LOWER(TRIM(email)) = ?', [$cleanLogin]);
                if (!str_contains($cleanLogin, '_')) {
                    $q->orWhereRaw('LOWER(TRIM(username)) LIKE ?', ['%_' . $cleanLogin]);
                }
            } else {
                $q->whereRaw('LOWER(TRIM(email)) = ?', [$cleanLogin]);
            }
        })->orderByDesc('id')->get();

        $inactiveMatch = null;

        foreach ($candidates as $candidate) {
            $matched = Hash::check($password, $candidate->password);
            if (! $matched && $candidate->password === $password) {
                $matched = true;
                $candidate->password = Hash::make($password);
                $candidate->save();
            }

            if (! $matched) {
                continue;
            }

            if ($this->isAccountActive($candidate)) {
                return ['user' => $candidate, 'error' => null];
            }

            $inactiveMatch = $candidate;
        }

        if ($inactiveMatch) {
            return [
                'user'  => null,
                'error' => 'Your account is inactive , Please contact admin',
            ];
        }

        return ['user' => null, 'error' => 'Wrong Login credentials entered.'];
    }

    protected function isAccountActive(User $user): bool
    {
        return (int) ($user->isDeleted ?? 0) !== 1
            && (int) ($user->status ?? 1) !== 0;
    }

    /**
     * Re-checks the client with admin-products (bypassing the lookup cache) so
     * a suspension, expiry or product change applies on the very next login.
     * If request host is central, resolves by user email.
     */
    protected function ensureTenantAllowsLogin(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $tenancy = app(TenantManager::class);
        if (! $tenancy->enabled()) {
            return null;
        }

        $login = trim((string)($request->input('username') ?? $request->input('email') ?? ''));

        $buildTenantErrorResponse = function (TenantResolutionException $ex) {
            $ctx = $ex->context ?? [];
            $isExpired = in_array(strtoupper((string) $ex->reason), ['EXPIRED', 'INACTIVE'], true)
                || str_contains(strtolower($ex->getMessage()), 'expired');
            $rawDate = $ctx['end_date'] ?? ($ctx['expiry_date'] ?? null);
            $formattedDate = $rawDate;
            if ($rawDate && $rawDate !== 'N/A') {
                try {
                    $formattedDate = Carbon::parse($rawDate)->format('d-m-Y');
                } catch (\Throwable) {}
            }

            return response()->json([
                'status'       => false,
                'is_expired'   => $isExpired,
                'tenant_error' => $ex->reason,
                'error'        => $ex->getMessage(),
                'plan_name'    => $ctx['plan_name'] ?? 'Starter',
                'end_date'     => $formattedDate ?: 'N/A',
                'expiry_date'  => $formattedDate ?: 'N/A',
            ], in_array($ex->status, [403, 404, 409, 503], true) ? $ex->status : 403);
        };

        // On central host (localhost, 127.0.0.1, etc.), identify tenant directly by user credentials
        if (\App\Http\Middleware\IdentifyTenant::isCentralHost($request->getHost())) {
            if (!empty($login)) {
                try {
                    $tenant = $tenancy->resolveByEmail($login, true);
                    if ($request->hasSession()) {
                        $request->session()->put('tenant_id', $tenant['id']);
                        $request->session()->put('tenant', $tenant);
                    }
                    return null;
                } catch (TenantResolutionException $userTenantError) {
                    return $buildTenantErrorResponse($userTenantError);
                }
            }
            return null;
        }

        try {
            // Check if request host matches a specific tenant subdomain
            $tenancy->resolveByHost($request->getHost(), true);
            return null;
        } catch (TenantResolutionException $e) {
            if (!empty($login)) {
                try {
                    $tenant = $tenancy->resolveByEmail($login, true);
                    if ($request->hasSession()) {
                        $request->session()->put('tenant_id', $tenant['id']);
                        $request->session()->put('tenant', $tenant);
                    }
                    return null;
                } catch (TenantResolutionException $userTenantError) {
                    return $buildTenantErrorResponse($userTenantError);
                }
            }

            return $buildTenantErrorResponse($e);
        }
    }

    // Logout User
    public function logout(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->forget(['tenant_id', 'tenant']);
        }
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function logoutapi(Request $request)
    {
        if ($request->user() && $request->user()->token()) {
            $request->user()->token()->revoke();
        }
        return response()->json(['status' => true, 'message' => 'Logout Successfully'], 200);
    }
}
