<?php

namespace App\Models;

use App\Http\Data\CompanyData;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

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


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
