<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class CustomInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'custom_invoice_item';
    protected $fillable = [
        'item',
        'quantity',
        'price',
        'purchase_status',
        'payment_status',
        'amount_total',
        'vendor_id',
        'customer_id',
        'invoice_id',
        'isDeleted',
        'created_at',
        'updated_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'item', 'id');
    }

}
