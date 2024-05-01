<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\Alignment;

trait FormattedText
{
    public Alignment $alignment = Alignment::left;
    public bool $bolded = false;
    public bool $underlined = false;
    public bool $inverted = false;
    public string $text = '';

    public abstract function getText(): string;

    public function center() {
        $this->alignment = Alignment::center;
    }

    public function setBold()
    {
        $this->bolded = true;
    }

    public function setUnderlined()
    {
        $this->underlined = true;
    }

    public function setInverted()
    {
        $this->inverted = true;
    }

    public function appendText(string $text)
    {
        $this->text .= $text;
    }
}
