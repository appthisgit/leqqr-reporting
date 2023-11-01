<?php

namespace App\Models;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'company',
        'order',
    ];

    protected $casts = [
        'company' => CompanyData::class,
        'order' => OrderData::class
    ];
}
