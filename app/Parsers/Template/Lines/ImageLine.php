<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\PrintSettings;

class ImageLine extends Line
{

    public function __construct(
        public string $image,
        PrintSettings $defaults,
    ) {
        parent::__construct($defaults);
    }
}
