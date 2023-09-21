<?php

namespace App\Http\DTOs\Leqqr;

class Variation
{

    public function __construct(
        public readonly string $key,
        public array $values,
    ) {
    }
}
