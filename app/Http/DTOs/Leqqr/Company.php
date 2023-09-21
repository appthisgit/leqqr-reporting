<?php

namespace App\Http\DTOs\Leqqr;

class Company
{
    public function __construct(
        public int $id,
        public string $GUID,
        public string $name,
    ){}
}
