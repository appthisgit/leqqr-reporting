<?php

namespace App\Http\DTOs\Out\Base\Lines;

class ImageLine extends Line
{

    public function __construct(
        public string $image,
    ) {
    }
}
