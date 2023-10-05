<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class VatData extends Data
{

    public function __construct(
        public readonly float $order_ex,
        /** @var VatRowData[] */
        public DataCollection $rows_vat,
        public readonly float $order_vat,
        public readonly float $discount_ex,
        public readonly float $discount_vat,
    ) {
    }

}
