<?php

namespace App\Support;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Company settings and plan notice shared with every view. With tenancy
 * enabled this must run after the tenant database is selected.
 */
class AppViewData
{
    public static function share(): void
    {
        $defaults = [
            'name'              => 'Garage ERP',
            'email'             => 'admin@gmail.com',
            'phone'             => '1234567890',
            'address'           => 'Surat, Gujarat',
            'currency_symbol'   => '₹',
            'currency_position' => 'left',
        ];

        try {
            $appSetting = Setting::first() ?? new Setting($defaults);
        } catch (\Exception $e) {
            $appSetting = new Setting($defaults);
        }

        View::share('appSetting', $appSetting);
        View::share('currentTenant', tenant());
        View::share('currentClientId', tenant('client_id'));

        $planExpiryNotice = [
            'show' => false,
            'is_expired' => false,
            'plan_id' => null,
            'plan_name' => null,
            'end_date' => null,
            'days_left' => null,
            'days_left_text' => null,
            'message' => null,
            'contact_name' => $appSetting->name ?? 'Support',
            'contact_email' => $appSetting->email ?? '',
            'contact_phone' => $appSetting->phone ?? '',
            'today' => Carbon::now('Asia/Kolkata')->toDateString(),
        ];

        $user = Auth::user();
        if ($user) {
            $plan = $user->getActivePlan();

            if ($plan && ! empty($plan->end_date)) {
                $now = Carbon::now('Asia/Kolkata');
                $endDate = Carbon::parse($plan->end_date, 'Asia/Kolkata')->endOfDay();
                $diffInDays = $now->diffInDays($endDate, false);

                if ($now->greaterThan($endDate)) {
                    $planExpiryNotice['show'] = true;
                    $planExpiryNotice['is_expired'] = true;
                    $planExpiryNotice['days_left'] = 0;
                    $planExpiryNotice['days_left_text'] = 'Expired';
                    $planExpiryNotice['message'] = 'Your subscription plan has expired. Please renew your plan to continue using the system.';
                } elseif ($diffInDays <= 30) {
                    $planExpiryNotice['show'] = true;
                    $planExpiryNotice['days_left'] = max(0, (int) ceil($diffInDays));
                    $planExpiryNotice['days_left_text'] = $diffInDays === 0 ? 'Expires today' : ($diffInDays === 1 ? '1 day left' : $diffInDays.' days left');
                    $planExpiryNotice['message'] = 'Your subscription plan will expire on '.$endDate->format('d-m-Y').'. Please renew to avoid interruption.';
                }

                $planExpiryNotice['plan_id'] = $plan->id;
                $planExpiryNotice['plan_name'] = $plan->name;
                $planExpiryNotice['end_date'] = $endDate->format('d-m-Y');
            }
        }

        View::share('planExpiryNotice', $planExpiryNotice);
    }
}
