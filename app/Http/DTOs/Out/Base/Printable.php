<?php

namespace App\Http\DTOs\Out\Base;

class Printable
{

    public function __construct(
        public array $lines = array(),
    ) {
    }
}
