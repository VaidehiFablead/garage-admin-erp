<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $fillable = [
        'order_number',
        'user_id',
        'car_model',
        'vehicle_no',
        'user_type',
        'discount',
        'tax_id',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_type',
        'cash_amount',
        'online_amount',
        'emi_duration',
        'emi_amount',
        'remaining_amount',
        'order_invoice',
        'isDeleted',
        'branch_id',
        'type',
        'remark',
        'gst_option',
        'paid_by_date',
        'created_by',
        'created_at',
        'updated_at ',
        
        'staff_id',
        'date',
        'next_warranty_date',
        'order_id',
        
    ];

     protected $appends = ['car_model_name'];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function subbranch()
    {
        return $this->belongsTo(Subbranch::class, 'branch_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function biller()
    {
        return $this->belongsTo(User::class, 'branch_id');
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function paymentStore()
    {
        return $this->hasMany(PaymentStore::class, 'order_id');
    }
    public function carModel()
    {
        return $this->belongsTo(AutoCarModel::class, 'car_model', 'id');
    }
   

    public function getCarModelNameAttribute()
    {
        return $this->carModel?->model_name ?? '';
    }
}
