<?php

namespace App\Helpers;

use Nette\Utils\Strings as UtilsStrings;

class Strings extends UtilsStrings
{
    public static function isEmptyOrValueNull(?string $text): bool
    {
        return $text == null || empty(trim($text)) || $text == 'null';
    }
}
