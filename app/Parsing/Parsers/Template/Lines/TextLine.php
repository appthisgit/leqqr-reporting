<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\Alignment;
use App\Helpers\ReceiptSettings;
use App\Helpers\TextMods;

class TextLine extends Line
{
    use FormattedText;

    public string $font;
    public int $fontSize;

    public function __construct(
        ReceiptSettings $defaults,
        string $text = ''
    ) {
        parent::__construct($defaults);
        $this->font = $defaults->font;
        $this->fontSize = $defaults->fontSize;
        $this->text = $text;
    }

    public function getText(): string
    {
        if ($this->inverted && $this->defaults->fontSize == $this->fontSize) {
            return TextMods::pad(
                $this->text,
                $this->defaults->widthCharAmount,
                match ($this->alignment) {
                    Alignment::left => STR_PAD_RIGHT,
                    Alignment::center => STR_PAD_BOTH,
                    Alignment::right => STR_PAD_LEFT,
                }
            );
        }

        return $this->text;
    }
}
