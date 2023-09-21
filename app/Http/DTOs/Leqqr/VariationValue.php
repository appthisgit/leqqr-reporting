<?php

namespace App\Http\DTOs\Leqqr;

class VariationValue {

    public function __construct(
        public string $name,
        public ?string $kitchen_info,
        public ?float $price,
        public ?float $vat_tarif,
        public ?int $external_id,
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
