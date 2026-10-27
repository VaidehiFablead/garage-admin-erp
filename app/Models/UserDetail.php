<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'city',
        'country',
        'isDeleted',
        'branch_id',
        'salary',
        'designation_id',
        'department_id',
        'face_photo',
        'joining_date',
        'shift_time',
        'working_location',
        'pan_number',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subbranch()
    {
        return $this->belongsTo(Subbranch::class, 'branch_id');
    }
}
