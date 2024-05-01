<?php

namespace App\Models;

use App\Http\Data\CompanyData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'guid',
        'name',
    ];


    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function toData(): CompanyData
    {
        return new CompanyData($this->id, $this->guid, $this->name);
    }

    public static function fromData(CompanyData $data): Company
    {
        return self::where('id', $data->id)->firstOr(function () use ($data) {
            return self::create([
                'id' => $data->id,
                'guid' => $data->guid,
                'name' => $data->name
            ]);
        });
    }
}
