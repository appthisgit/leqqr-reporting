<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\ReceiptSettings;
use App\Helpers\TextMods;

class TableCell extends Line
{
    use FormattedText;

    protected string $text = '';
    public int $span = 1;

    public function __construct(
        ReceiptSettings $defaults,
        string $text = '',
        public int $maxLength = -1,
        public int $pad_type = STR_PAD_RIGHT
    ) {
        parent::__construct($defaults);
        $this->text = $text;
    }

    public function appendText(string $text)
    {
        $this->text .= $text;
    }

    public function getText(): string
    {
        if ($this->maxLength < 0) {
            $this->maxLength = mb_strlen($this->text);
        }
        if ($this->center()) {
            $this->pad_type = STR_PAD_BOTH;
        }

        //TODO: pad_type STR_PAD_NONE
        return TextMods::pad($this->text, $this->maxLength, $this->pad_type);
    }
}
