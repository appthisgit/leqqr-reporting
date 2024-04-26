<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\Alignment;
use App\Helpers\ReceiptSettings;
use App\Helpers\TextMods;

class TableCell extends Line
{
    use FormattedText;

    public int $span = 1;

    public function __construct(
        ReceiptSettings $defaults,
        string $text = '',
        public int $maxLength = -1,
        Alignment $alignment = Alignment::left
    ) {
        parent::__construct($defaults);
        $this->text = $text;
        $this->alignment = $alignment;
    }

    public function appendText(string $text)
    {
        $this->text .= $text;
    }

    public function getText(): string
    {
        if ($this->defaults->widthCharAmount == null) {
            return $this->text;
        }
        if ($this->maxLength < 0) {
            $this->maxLength = mb_strlen($this->text);
        }

        return TextMods::pad($this->text, $this->maxLength, $this->alignment->value);
    }
}
