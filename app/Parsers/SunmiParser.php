<?php

namespace App\Parsers;

use App\Parsers\Printable\Printable;

class SunmiParser
{

    public function sendToPrinter(string $uid, Printable $printable)
    {
        $value = '';

        foreach ($printable->lines as $line) {

            switch (get_class($line)) {
                case TextLine::class:
                    $value .= '\r\n' . $line->text;
                    break;
                case ImageLine::class:
                    $value .= '\r\n <images are note supported>';
                    break;
            }
        }

        // send $value to sunmi
    }

}
