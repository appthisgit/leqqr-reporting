<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\Margins;
use App\Helpers\ReceiptSettings;

abstract class Line
{
    public bool $centered;
    public Margins $margins;
    protected string $value;

    public function __construct(
        public ReceiptSettings $defaults
    ) {
        $this->margins = $defaults->lineMargins->copy();
        $this->centered = false;
    }

    public function center() {
        $this->centered = true;
    }

    public abstract function __toString();
}
