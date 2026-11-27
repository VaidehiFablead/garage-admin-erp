<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Type extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_name',
        'branch_id',
        'is_deleted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Get the materials associated with this type.
     */
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'material_type', 'type_id', 'material_id')
            ->withTimestamps();
    }
}
