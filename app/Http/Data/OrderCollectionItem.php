<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class OrderCollectionItem extends Data
{

    public function __construct(
        public readonly int $id,
        public readonly string $confirmation_code,
        public readonly bool $is_seen,
        public readonly string $shipmentType,
        public readonly string $address,
        public readonly string $postal,
        public readonly string $city,
        public readonly string $phone,
        public readonly string $email,
        public readonly string $createdAt,
    ) {
    }

    public function isSeen() : bool {
        return $this->is_seen == 1;
    }

    public function isDelivery() : bool
    {
        return $this->shipmentType == 'delivery';
    }

    public function isPickup() : bool
    {
        return $this->shipmentType == 'pickup';
    }
}
