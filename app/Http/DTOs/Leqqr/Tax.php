<?php

namespace App\Http\DTOs\Leqqr;

class Tax
{

    public function __construct(
        public float $tarif,
        public float $price,
    ) {
    }

}
