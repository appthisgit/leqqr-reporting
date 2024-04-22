<?php

namespace App\Parsers\Template\Lines;

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
            return $this->text;
            // return TextMods::pad($this->text, $this->defaults->widthCharAmount, $this->centered);
        }
        // if ($this->wrapped && $this->defaults->fontSize == $this->fontSize) {
        //     return TextMods::wordwrap($this->value, $this->defaults->widthCharAmount);
        // }

        return $this->text;
    }
}
