<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class OrderCollectionItem extends Data
{

    public function __construct(
        public readonly int $id,
        public readonly string $confirmation_code,
        public readonly string $shipment_type,
        public readonly string $address,
        public readonly string $postal,
        public readonly string $city,
        public readonly string $phone,
        public readonly string $email,
        public readonly string $created_at,
    ) {
    }

    public function isDelivery() : bool
    {
        return $this->shipment_type == 'delivery';
    }

    public function isPickup() : bool
    {
        return $this->shipment_type == 'pickup';
    }
}
