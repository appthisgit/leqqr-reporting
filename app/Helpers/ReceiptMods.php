<?php

namespace App\Helpers;

class ReceiptMods extends TextMods
{
    public static function divider(string $character, int $length): string
    {
        $divider = '';
        for ($times = $length; $times > 0; $times--) {
            $divider .= $character;
        }
        return $divider;
    }

}
