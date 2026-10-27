<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoCarModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_name',
        'model_brand',
        'branch_id',
        'is_deleted',
    ];
}

