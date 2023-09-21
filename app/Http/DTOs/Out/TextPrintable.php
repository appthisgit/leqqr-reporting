<?php

namespace App\Http\DTOs\Out;

use App\Http\DTOs\Out\Base\Printable;

class TextPrintable extends Printable
{

    public function getValue() : string
    {
        $value = '';

        foreach ($this->lines as $line) {

            switch (get_class($line)) {
                case TextLine::class:
                    $value .= '\r\n' . $line->text;
                    break;
                case ImageLine::class:
                    $value .= '\r\n <images are note supported>';
                    break;
            }
        }

        return $value;
    }
}
