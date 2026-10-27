<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subbranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'subbranch_name',
        'address',
        'phone',
        'email',
        'city',
        'status',
        'pincode',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class, 'branch_id');
    }

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class, 'branch_id');
    }
} 