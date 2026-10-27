<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabourService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'branch_id',
        'is_deleted',
    ];
}
