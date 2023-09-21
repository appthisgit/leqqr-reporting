<?php

namespace App\Http\DTOs\Leqqr;

class Product
{

    public function __construct(
        public array $variations,
        public int $amount,
        public string $name,
        public string $kitchen_info,
        public string $notes,
        public float $subtotal,
        public float $price,
        public float $price_discount,
        public bool $printable,
        public float $vat_tarif,
        public string $zone,
        public int $external_id,
    ) {
    }

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
