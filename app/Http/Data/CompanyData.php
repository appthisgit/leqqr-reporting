<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class CompanyData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $guid,
        public readonly string $name,
    ){}
}
