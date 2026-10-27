<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseAutoCare extends Model
{
    use HasFactory;

    protected $table = 'purchases_autocare';

    protected $fillable = [
        'vendor_id',
        'vendor_phone',
        'bill_no',
        'invoice_no',
        'payable_amount',
        'payment_mode',
        'paid_type',
        'cash_amount',
        'amount',
        'remaining_amount',
        'upi_amount',
        'pending_amount',
        'comment',
        'sub_admin_id',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
    public function payments()
    {
        return $this->hasMany(PaymentStore::class, 'purchase_id', 'id');
    }
}
