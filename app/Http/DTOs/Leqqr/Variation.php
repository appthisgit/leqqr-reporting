<?php

namespace App\Http\DTOs\Leqqr;

class Variation
{

    public function __construct(
        public string $key,
        public array $values,
    ) {
    }
}
