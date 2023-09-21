<?php

namespace App\Http\DTOs\Leqqr;

use Illuminate\Support\Facades\Log;

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

    public function inFilters(bool $filterPrintable, ?string $filterZone): bool
    {
        $printableCheck = !$filterPrintable || ($filterPrintable && $this->printable);
        $zoneCheck = empty($filterZone) || $filterZone == $this->zone;

        if (!$printableCheck)
        {
            Log::debug('(skipped product) "' + $this->name + '" is not set as printable');
        }
        if (!$zoneCheck){
            Log::debug('(skipped product) "' + $this->name + '" is not in correct zone');
        }

        return $printableCheck && $zoneCheck;
    }
}
