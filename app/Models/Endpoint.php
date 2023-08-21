<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'template_id',
        'type',
        'target',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
