<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ReceiptMods extends TextMods
{
    public static function divider(string $character, int $length): string
    {
        $divider = '';
        for ($times = $length; $times >= 0; $times--) {
            $divider .= $character;
        }
        return $divider;
    }

    public static function formatPrice(string $description, float $price, float $width, int $right): string
    {
        $left = $width - TextMods::SPACE - $right;

        $result = '';
        $col_1 = TextMods::multipad_array($description, $left);
        $col_2 = '€' . Str::padLeft($price, $right);

        if (count($col_1) == 1) {
            $result = $col_1[0] . ' ' . $col_2;
        }
        else {
            $shownPrice = false;

            foreach ($col_1 as $line) {
                $result .= $line;

                if (!$shownPrice) {
                    $result .= ' ' . $col_2;
                    $shownPrice = true;
                }
            }

        }

        return $result;
    }
}
