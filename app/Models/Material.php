<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'materials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'branch_id',
        'is_deleted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price'      => 'decimal:2',
        'is_deleted' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'type_ids',
    ];

    /**
     * Get the types associated with this material.
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'material_type', 'material_id', 'type_id')
            ->withTimestamps();
    }

    /**
     * Accessor for related type IDs for easy form pre-filling.
     */
    public function getTypeIdsAttribute(): array
    {
        if ($this->relationLoaded('types')) {
            return $this->types->pluck('id')->values()->all();
        }

        return $this->types()->pluck('types.id')->all();
    }
}
