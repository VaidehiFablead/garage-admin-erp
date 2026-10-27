<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\UserPermission;
use App\Http\ViewComposers\ReminderComposer;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\PaymentStore;
use App\Models\JobCard;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
            } catch (\Exception $e) {
                // Optional: Log error or ignore silently
            }
        }

        // app()->singleton('hasPermission', function () {
        //     return function ($moduleId, $action) {
        //         $user = Auth::user(); // Use default auth guard for web

        //         if (!$user) return false;

        //         // Grant all permissions to admin users
        //         if ($user->role == 'admin' || $user->role_id == 1 || $user->role == 'sub-admin') {
        //             return true;
        //         }

        //         // Check permission from DB
        //         return UserPermission::where('user_id', $user->id)
        //             ->where('module_id', $moduleId)
        //             ->where("{$action}", true)
        //             ->exists();
        //     };
        // });
        app()->singleton('hasPermission', function () {
    return function ($moduleId, $action) {

        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin and Sub Admin have all permissions
        if ($user->role == 'admin' || $user->role_id == 1 || $user->role == 'sub-admin') {
            return true;
        }

        // Map UI action names to DB column names
        $actionMap = [
            'list'   => 'view',
            'view'   => 'view',
            'insert' => 'add',
            'add'    => 'add',
            'edit'   => 'edit',
            'delete' => 'delete',
        ];

        $column = $actionMap[$action] ?? null;

        if (!$column) {
            return false;
        }

        return UserPermission::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->where($column, 1)
            ->exists();
    };
});

        // ✅ Add global data for Today Appointments Modal
        View::composer('partials.todayAppointmentsModal', function ($view) {
            $today = Carbon::today()->format('Y-m-d');
            $nextWeek = Carbon::today()->addDays(7);

            $user = Auth::user();
            $subAdminId = session('selectedSubAdminId') ?? ($user ? $user->id : null);

            // ============ Today's Appointments ============
            $appointments = Appointment::whereDate('appointment_date', $today)
                ->where('branch_id', $subAdminId)
                ->where('is_deleted', 0)
                ->get();

            // ============ Upcoming EMIs ============
            $upcomingEmis = PaymentStore::select(
                'payment_store.*',
                'job_cards.vehicle_no',
                'auto_car_models.model_name',
                'job_cards.total_subtotal_amount',
                'users.name as customer_name'
            )
                ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
                ->join('users', 'job_cards.customer_id', '=', 'users.id')
                ->join('auto_car_models', 'job_cards.model', '=', 'auto_car_models.id')
                ->where('payment_store.payment_method', 'EMI')
                ->where('payment_store.isDeleted', 0)
                ->where('job_cards.subbranch_id', $subAdminId)
                ->get()
                ->filter(function ($payment) use ($today, $nextWeek) {
                    $baseDate = Carbon::parse($payment->payment_date);
                    $dueDate = $baseDate->copy()->addMonths($payment->emi_month);
                    $payment->emi_due_date = $dueDate->format('d-m-Y');
                    return $dueDate->between($today, $nextWeek);
                });

            // ============ Upcoming Cash/Online Payments ============
            $upcomingPayments = JobCard::select(
                'job_cards.*',
                'auto_car_models.model_name',
                'users.name as customer_name'
            )
                ->join('users', 'job_cards.customer_id', '=', 'users.id')
                ->join('auto_car_models', 'job_cards.model', '=', 'auto_car_models.id')
                ->whereIn('job_cards.payment_method', ['Cash', 'Online'])
                ->where('job_cards.isDeleted', 0)
                ->where('job_cards.subbranch_id', $subAdminId)
                ->get()
                ->filter(function ($job) use ($today, $nextWeek) {
                    if (!$job->next_pending_date) return false;
                    $dueDate = Carbon::parse($job->next_pending_date);
                    $job->emi_due_date = $dueDate->format('d-m-Y');
                    return $dueDate->between($today, $nextWeek);
                });

            // ============ Customers Completing 6 Months ============
            $customers6Months = JobCard::select(
                'job_cards.*',
                'auto_car_models.model_name',
                'users.name as customer_name'
            )
                ->join('users', 'job_cards.customer_id', '=', 'users.id')
                ->join('auto_car_models', 'job_cards.model', '=', 'auto_car_models.id')
                ->where('job_cards.isDeleted', 0)
                ->where('job_cards.subbranch_id', $subAdminId)
                ->orderBy('job_cards.id', 'desc')
                ->get()
                ->filter(function ($job) use ($today) {
                    if (!$job->record_date) return false;
                    $recordDate = Carbon::parse($job->record_date);
                    $dueDate = $recordDate->copy()->addMonths(6);
                    $job->six_month_due_date = $dueDate->format('d-m-Y');
                    return $dueDate->isSameDay($today);
                })
                ->take(5);

            // ✅ Share with modal view
            $view->with(compact(
                'appointments',
                'upcomingEmis',
                'upcomingPayments',
                'customers6Months'
            ));
        });
        View::composer('layout.header', ReminderComposer::class);
    }
}
