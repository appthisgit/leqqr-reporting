<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'printable' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'zone',
        'printable',
        'content',
    ];

    public function endpoints(): HasMany
    {
        return $this->hasMany(Endpoint::class);
    }
}
