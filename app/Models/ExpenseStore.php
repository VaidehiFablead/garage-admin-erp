<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseStore extends Model
{
    use HasFactory;

    protected $table = 'expense_store';

    protected $fillable = [
        'user_id',
        'jobcard_id',
        'vendor_name',
        'items',
        'amount',
        'payment_method',
        'isDeleted',
    ];
}
