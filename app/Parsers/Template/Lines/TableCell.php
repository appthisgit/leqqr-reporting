<?php

namespace App\Parsers\Template\Lines;

class TableCell
{

    use FormattedText;

    public function __construct(
        string $text,
        public int $length = 1,
        public string $pad = 'right'
    ) {
        $this->text = $text;
    }

    public function alignRight()
    {
        $this->pad = 'left';
    }

    public function getText(): string
    {
        // TODO: make this work
        switch ($this->pad) {
            case 'right':
                return $this->text;
            case 'left':
                return $this->text;
            case 'both':
            case 'none':
            default:
                // TODO: make this
                return $this->text;
        }
    }
}
