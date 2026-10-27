<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    use HasFactory;
    protected $table = 'job_cards';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'vehicle_no',
        'model',
        'engine_no',
        'chassis_no',
        'km_hours',
        'due_km_hours',
        'customer_id',
        'address',
        'mobile',
        'job_card_number',
        'category',
        'technician',
        'date',
        'next_due_date',
        'labour_details',
        'labours_gst',
        'parts_details',
        'parts_gst',
        'total_labour',
        'total_parts',
        'total_subtotal_amount',
        'remaining_amount',
        'comment',
        'is_printable',
        'remark',
        'remark_non_printable',
        'subbranch_id',
        'payment_method',
        'payment_type',
        'online_amount',
        'cash_amount',
        'emi_months',
        'emi_duration',
        'order_number',
        'discount',
        'user_id',
        'type',
        'quotation_status',
        'record_date',
        'start_date',
        'end_date',
        'customer_name',
        'status',
        'bill_no',
        'next_pending_date',
        'labour_discount',
        'parts_discount',
        'isDeleted',
        'gst',
        'old_job_card',
        'extra_pay',


    ];

    public function expenseStores()
    {
        return $this->hasMany(ExpenseStore::class, 'jobcard_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentStore::class, 'jobcard_id')->where('isDeleted', 0);
    }

    public function autocarmodel()
    {
        return $this->belongsTo(AutoCarModel::class, 'model');
    }

    public function technicianUser()
    {
        return $this->belongsTo(User::class, 'technician');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function paymentStore()
    {
        return $this->hasMany(PaymentStore::class, 'jobcard_id')->where('isDeleted', 0);
    }
    public function autoCustomer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function branch()
    {
        return $this->belongsTo(User::class, 'subbranch_id');
    }

}
