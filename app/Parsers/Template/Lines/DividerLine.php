<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\ReceiptSettings;

class DividerLine extends TextLine
{

    private static $divider = '';

    public function __construct(
        ReceiptSettings $defaults,
    ) {
        parent::__construct($defaults, self::$divider);

        if (self::$divider == '') {
            for ($times = $this->defaults->widthCharAmount; $times > 0; $times--) {
                self::$divider .= $this->defaults->stripeChar;
            }
            $this->text = self::$divider;
        }
    }
}
