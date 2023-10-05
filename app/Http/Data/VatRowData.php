<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class VatRowData extends Data
{

    public function __construct(
        public readonly string $code,
        public readonly float $tarif,
        public readonly float $subtotal,
        public readonly float $vat_value,
        public readonly float $subtotal_ex,
    ) {
    }

}
