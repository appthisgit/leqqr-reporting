<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\Margins;
use App\Helpers\PrintSettings;

abstract class Line
{
    public bool $centered;
    public Margins $margins;
    protected string $value;

    public function __construct(
        public PrintSettings $defaults
    ) {
        $this->margins = $defaults->lineMargins->copy();
        $this->centered = false;
    }

    public function center() {
        $this->centered = true;
    }

    public abstract function __toString();
}
