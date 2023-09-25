<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class VariationData extends Data
{

    public function __construct(
        public readonly string $key,
        /** @var VariationValueData[] */
        public DataCollection $values,
    ) {
    }
}
