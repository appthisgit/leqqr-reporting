<?php

namespace App\Helpers;

use Nette\Utils\Strings as UtilsStrings;

class Strings extends UtilsStrings
{
    public static function isEmptyOrValueNull(?string $text): bool
    {
        return $text == null || empty(trim($text)) || $text == 'null';
    }

    public static function isNotEmptyOrValueNull(?string $text): bool
    {
        return !self::isEmptyOrValueNull($text);
    }
}
