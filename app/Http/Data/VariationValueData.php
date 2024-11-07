<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class VariationValueData extends Data {

    public function __construct(
        public readonly int $id,
        public readonly ?string $external_id,
        public readonly string $name,
        public readonly ?string $kitchen_info,
        public readonly ?float $price,
        public readonly int $amount,
        public readonly float $vat_tarif,
        public readonly int $order,
    ) {}

    public function hasTax(): bool
    {
        return !empty($this->vat_tarif);
    }

    public function getTax(): float
    {
        return ($this->hasTax() && $this->price > 0) ?
            $this->price / (100 + $this->vat_tarif) * $this->vat_tarif : 0;
    }

}
