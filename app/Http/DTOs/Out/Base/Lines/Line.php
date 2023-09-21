<?php

namespace App\Http\DTOs\Out\Base\Lines;

use App\Helpers\Margins;

abstract class Line
{

    public function __construct(
        public array $centered,
        public Margins $margins,
    ) {
    }

    public function center() {
        $this->centered = true;
    }
}
