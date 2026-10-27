<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $table = 'taxes'; // Specify the table name if it's different from the model name
    
    protected $fillable = [
        'branch_id',
        'tax_name',
        'tax_rate',
        'status',
        'isDeleted',
        'created_at',
        'updated_at',
    ];
}
