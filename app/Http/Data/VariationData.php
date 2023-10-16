<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class VariationData extends Data
{

    public function __construct(
        /** @var VariationValueData[] */
        public DataCollection $selected,
        public readonly int $id,
        public readonly string $overview_title,
        public readonly string $type,
        public readonly float $subtotal,
    ) {
        $selected->toCollection()->sortBy('order');
    }
}
