<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class TextMods
{
    const SPACE = 1;

    public static function pad(string $text, int $length, bool $center = false): string
    {
        $lines = array();
        foreach (TextMods::wordwrap_toArray($text, $length) as $line) {
            $lines[] = str_pad($line, $length, ' ', ($center) ? STR_PAD_BOTH : STR_PAD_RIGHT);
        }
        return implode("\r\n", $lines);
    }

    public static function wordwrap(string $value, int $maxLength): string
    {
        return implode("\r\n", self::wordwrap_toArray($value, $maxLength));
    }

    public static function wordwrap_toArray(string $value, int $maxLength): array
    {
        $values = array();

        // value doesn't fit inside maxLength
        if (strlen($value) > $maxLength && $maxLength > 0) {

            $line = '';
            $words = explode("\\s+", str_replace('-', '- ', trim($value)));
            $lastWasLine = false;

            foreach ($words as $word) {
                // Fits in the line
                if (strlen($line) + strlen($word) + self::SPACE <= $maxLength) {
                    $line .= ((!empty($line) && !$lastWasLine) ? ' ' : '') . $word;

                    if (Str::endsWith($word, '-')) {
                        $lastWasLine = true;
                    }
                }
                // Doesn't fit because the word is longer than the whole line
                elseif (strlen($word) > $maxLength) {
                    
                    // Loop until the word is completely written to lines
                    while (strlen($word) > $maxLength) {

                        // check if there's space for two characters in the current line
                        if (strlen($line) + self::SPACE + 2 <= $maxLength) {
                            // calc available character places
                            $availableCharacters = $maxLength;
                            if (!empty($line)) {
                                $availableCharacters -= strlen($line) + self::SPACE;
                            }

                            // fits the (rest) of the word within the available characters left
                            if ($availableCharacters >= strlen($word)) {
                                /**
                                 * Write the (rest) of the word to the current line
                                 * don't save the currentLine, possible room left
                                 * decrease size of the word with what's already written
                                 */
                                $line = ((!empty($line)) ? $line . ' ' : '') . $word;
                                $word = '';
                            }
                            // doesn't fit, add substring
                            else {
                                /**
                                 * Write max amount of characters to current line
                                 * append the current line to values
                                 * start a new empty current line
                                 * decrease size of the word with what's already written
                                 */
                                $wordSubstring = substr($word, 0, $availableCharacters);
                                $values[] = ((!empty($line)) ? $line . ' ' : '') . $wordSubstring;
                                $line = '';
                                $word = substr($word, $availableCharacters);
                            }
                        }
                        // no space left for more than two characters
                        else {
                            // Write away the line, continue on a new line
                            $values[] = $line;
                            $line = '';
                        }
                    }
                }
                // doesn't fit because word makes line to long
                else {
                    $values[] = $line;
                    $line = $word;
                }

                $lastWasLine = false;
            }

            // add rest of line
            if (!empty($line)) {
                $values[] = $line;
            }
        }
        // value actually fits inside a single line
        else {
            $values[] = $value;
        }

        return $values;
    }

    public static function multipad(string $value, $length): string
    {
        return implode("\r\n", self::multipad_array($value, $length));
    }

    public static function multipad_array(string $value, $length): array
    {
        $lines = array();

        // value already too long
        if (strlen($value) > $length) {
            /**
             * Wordwrap without amount, and shorter width re-add amount pad
             * first line right pad other lines left to match after amount
             */
            $value_exploded = explode("\\s+", $value);
            $toPadString = $value_exploded[0];
            $amountPadLeft = strlen($value_exploded[0] + self::SPACE);

            // string started with a space, so the first values was ""
            if (empty($toPadString) && count($value_exploded) >= 2)
            {
                $toPadString = ' ' . $value_exploded[1];
                $amountPadLeft = strlen($toPadString) + self::SPACE;
            }

            $lines = self::wordwrap_toArray(substr($value, $amountPadLeft), $length - $amountPadLeft);
            $lines[0] = $toPadString . ' ' . Str::padRight($lines[0], $length - $amountPadLeft);

            for ($i = 1; $i < count($lines); $i++) {
                $lines[$i] = Str::padLeft($lines[$i], strlen($lines[$i]) + $amountPadLeft);
            }
        }
        // pad until long enough
        else {
            $lines[] = Str::padRight($value, $length);
        }

        return $lines;
    }
}
