<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomInvoice extends Model
{
    use HasFactory;
    protected $table = 'custom_invoice'; // Specify the table name if different from the model name
    protected $fillable = [
        'branch_id',
        'invoice_number',
        'vendor_id',
        'customer_id',
        'products',
        'total_amount',
        'paid',
        'discount',
        'shipping',
        'grand_total',
        'status',
        'taxes',
        'isDeleted',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'products' => 'array', // Automatically convert JSON to array
    ];
}
