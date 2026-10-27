<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\PaymentStore;

class ReminderComposer
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request; // so we can use query params
    }

    public function compose(View $view)
    {
        // Get user info
        $user     = auth()->user();
        $userRole = $user->role ?? '';
        $userId   = $user->id ?? null;

        // Determine branch ID filter
        $selectedSubAdminId = $this->request->query('sub_branch_id');
        if (!empty($selectedSubAdminId)) {
            $filterId = (int) $selectedSubAdminId; // Explicit branch
        } else {
            $filterId = $userId; // Default to user's own branch
        }

        $today    = Carbon::today()->format('Y-m-d');
        $nextWeek = Carbon::today()->addDays(7);

        // Today's appointments
        $appointments = Appointment::whereDate('appointment_date', $today)
            ->where('branch_id', $filterId)
            ->where('is_deleted', 0)
            ->get();

        // Upcoming EMIs (next 7 days)
        $upcomingEmis = PaymentStore::select(
                'payment_store.*',
                'job_cards.vehicle_no',
                'auto_car_models.model_name as model_name',
                'job_cards.total_subtotal_amount',
                'users.name as customer_name'
            )
            ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
            ->join('users', 'job_cards.customer_id', '=', 'users.id')
            ->join('auto_car_models', 'job_cards.model', '=', 'auto_car_models.id')
            ->where('payment_store.payment_method', 'EMI')
            ->where('payment_store.isDeleted', 0)
            ->where('job_cards.subbranch_id', $filterId)
            ->get()
            ->filter(function ($payment) use ($today, $nextWeek) {
                $dueDate = Carbon::parse($payment->created_at)->addMonths($payment->emi_month);
                $payment->emi_due_date = $dueDate->format('d-m-Y');
                return $dueDate->between($today, $nextWeek);
            });

        // Send to the view
        $view->with([
            'appointments' => $appointments,
            'upcomingEmis' => $upcomingEmis
        ]);
    }
}
