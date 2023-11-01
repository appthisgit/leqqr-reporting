<?php

namespace App\Http\Data;

use Illuminate\Support\Facades\Log;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ProductData extends Data
{

    public function __construct(
        /** @var VariationData[] */
        public DataCollection $variations,
        public readonly int $amount,
        public readonly string $name,
        public readonly ?string $kitchen_info,
        public readonly ?string $notes,
        public readonly float $subtotal,
        public readonly float $price,
        public readonly ?float $price_discount,
        public readonly ?bool $printable,
        public readonly ?float $vat_tarif,
        public readonly ?string $zone,
        public readonly ?int $external_id,
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
            Log::debug('Product skipped! "' . $this->name . '" is not set as printable');
        }
        if (!$zoneCheck){
            Log::debug('Product skipped! "' . $this->name . '" is not in correct zone (product: "'.$this->zone.'", filter: "'.$filterZone.'")');
        }

        return $printableCheck && $zoneCheck;
    }
}
