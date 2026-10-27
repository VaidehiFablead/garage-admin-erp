<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'role',
        'password',
        'state_code',
        'state_name',
        'isDeleted',
        'profile_image',
        'gst_num',
        'haspermission',    
        'branch_id',
        'face_descriptor',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'face_descriptor' => 'array',
    ];

    public function details()
    {
        return $this->hasOne(UserDetail::class, 'user_id', 'id');
    }
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }
public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    /**
     * Get the active plan for the user/admin.
     */
    public function getActivePlan()
    {
        if ($this->plan_id && $this->plan) {
            return $this->plan;
        }

        if (! empty($this->branch_id)) {
            $plan = \App\Models\Plan::where('sub_branch_id', $this->branch_id)->where('is_active', 1)->latest('id')->first();
            if ($plan) {
                return $plan;
            }
        }

        return \App\Models\Plan::where('is_active', 1)->latest('id')->first();
    }

    /**
     * Check plan expiry status using Carbon date comparison.
     */
    public function checkPlanExpiryStatus(): array
    {
        $plan = $this->getActivePlan();

        if (! $plan || empty($plan->end_date)) {
            return [
                'status' => 'active',
                'message' => null,
                'end_date' => null,
            ];
        }

        $today = Carbon::today('Asia/Kolkata');
        $endDate = Carbon::parse($plan->end_date, 'Asia/Kolkata')->startOfDay();
        $warningStartDate = $endDate->copy()->subDays(30);

        if ($today->gt($endDate)) {
            return [
                'status' => 'expired',
                'message' => 'Your subscription plan has expired. Please renew your plan to continue using the system.',
                'end_date' => $endDate->format('d-m-Y'),
            ];
        }

        if ($today->gte($warningStartDate)) {
            $formattedDate = $endDate->format('d-m-Y');
            return [
                'status' => 'warning',
                'message' => "⚠️ Your subscription plan will expire on {$formattedDate}. Please renew your plan to avoid interruption of service.",
                'end_date' => $formattedDate,
            ];
        }

        return [
            'status' => 'active',
            'message' => null,
            'end_date' => $endDate->format('d-m-Y'),
        ];
    }
    public function permissions()
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }
}
