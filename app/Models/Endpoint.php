<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endpoint extends Model
{
    use HasFactory;

        /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'filter_printable' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'company_id',
        'template_id',
        'type',
        'target',
        'filter_terminal',
        'filter_zone',
        'filter_printable',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
