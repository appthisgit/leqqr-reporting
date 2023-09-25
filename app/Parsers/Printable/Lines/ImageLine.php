<?php

namespace App\Parsers\Printable\Lines;

class ImageLine extends Line
{

    public function __construct(
        public string $image,
    ) {
    }
}
