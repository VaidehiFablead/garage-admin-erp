<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStore extends Model
{
    use HasFactory;

    protected $table = 'payment_store';

    protected $fillable = [
        'user_id',
        'order_id',
        'jobcard_id',
        'purchase_id',
        'payment_amount',
        'payment_date',
        'payment_method',
        'payment_type',
        'cash_amount',
        'upi_amount',
        'emi_month',
        'remaining_amount',
        'discount_amount',
        'remark',
        'isDeleted',
        'created_at',
        'updated_at',
    ];

    // Optional: relations
    public function jobcard()
    {
        return $this->belongsTo(JobCard::class, 'jobcard_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
   
}
