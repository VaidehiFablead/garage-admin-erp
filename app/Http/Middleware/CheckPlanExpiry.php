<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanExpiry
{
    /**
     * Handle an incoming request.
     * Checks if active plan is expired for admin users and logs out if so.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'admin' || $user->role !== 'staff') {
                $planCheck = $user->checkPlanExpiryStatus();

                if ($planCheck['status'] === 'expired') {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $latestPlan = $user->getActivePlan();
                    $planName = $latestPlan?->name ?? 'Starter';
                    $endDate = $planCheck['end_date'] ?? 'N/A';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'status'      => false,
                            'is_expired'  => true,
                            'error'       => $planCheck['message'],
                            'plan_name'   => $planName,
                            'end_date'    => $endDate,
                            'expiry_date' => $endDate,
                        ], 403);
                    }

                    return redirect('/')
                        ->with('error', $planCheck['message'])
                        ->with('plan_name', $planName)
                        ->with('end_date', $endDate);
                } elseif ($planCheck['status'] === 'warning') {
                    view()->share('planExpiryWarningMessage', $planCheck['message']);
                    if (! session()->has('plan_expiry_warning')) {
                        session()->flash('plan_expiry_warning', $planCheck['message']);
                    }
                }
            }
        }

        return $next($request);
    }
}
