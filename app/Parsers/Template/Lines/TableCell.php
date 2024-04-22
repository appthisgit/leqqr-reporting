<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\TextMods;

class TableCell
{
    use FormattedText;

    public function __construct(
        string $text,
        private int $length = -1,
        public int $pad_type = STR_PAD_RIGHT
    ) {
        if ($length < 0) {
            $this->length = mb_strlen($text);
        }

        $this->text = $text;
    }

    public function getText(): string
    {
        if ($this->center()) {
            $this->pad_type = STR_PAD_BOTH;
        }
        return TextMods::pad($this->text, $this->length, $this->pad_type);
    }
}
