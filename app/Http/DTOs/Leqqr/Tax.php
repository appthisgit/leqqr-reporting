<?php

namespace App\Http\DTOs\Leqqr;

class Tax
{

    public function __construct(
        public readonly float $tarif,
        public float $total,
    ) {
    }

}
