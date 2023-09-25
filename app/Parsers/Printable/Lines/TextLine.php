<?php

namespace App\Parsers\Printable\Lines;

use App\Helpers\PrintSettings;

class TextLine extends Line
{

    public string $font;
    public int $fontSize;
    public bool $bolded;

    public function __construct(
        protected string $text,
        PrintSettings $defaults,
    ) {
        parent::__construct($defaults);
        $this->font = $defaults->font;
        $this->fontSize = $defaults->fontSize;
        $this->bolded = $defaults->bold;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setBold()
    {
        $this->bolded = true;
    }

    public function appendText(string $text)
    {
        $this->text .= $text;
    }
}
