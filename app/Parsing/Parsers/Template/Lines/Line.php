<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\Margins;
use App\Helpers\ReceiptSettings;

abstract class Line
{
    public Margins $margins;

    public function __construct(
        public ReceiptSettings $defaults
    ) {
        $this->margins = $defaults->lineMargins->copy();
        
    }
    
    public function __toString()
    {
        return get_class($this) . ': ' . json_encode($this);
    }
}
