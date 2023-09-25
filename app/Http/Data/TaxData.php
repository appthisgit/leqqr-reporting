<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class TaxData extends Data
{

    public function __construct(
        public readonly float $tarif,
        public float $total,
    ) {
    }

}
