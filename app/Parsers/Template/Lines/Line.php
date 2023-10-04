<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\Margins;
use App\Helpers\PrintSettings;

abstract class Line
{
    public bool $centered;
    public Margins $margins;

    public function __construct(
        PrintSettings $defaults
    ) {
        $this->centered = $defaults->center;
        $this->margins = $defaults->lineMargins->copy();
    }

    public function center() {
        $this->centered = true;
    }
}
