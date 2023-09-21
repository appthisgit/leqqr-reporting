<?php

namespace App\Http\DTOs\Leqqr;

class OrderCollectionItem
{

    public function __construct(
        public int $id,
        public string $confirmation_code,
        public bool $is_seen,
        public string $shipmentType,
        public string $address,
        public string $postal,
        public string $city,
        public string $phone,
        public string $email,
        public string $createdAt,
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
