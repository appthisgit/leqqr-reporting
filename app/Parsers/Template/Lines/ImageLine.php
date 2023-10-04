<?php

namespace App\Parsers\Template\Lines;

class ImageLine extends Line
{

    public function __construct(
        public string $image,
    ) {
    }
}
