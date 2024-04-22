<?php

namespace App\Parsing\Parsers\Template\Lines;


trait FormattedText
{
    public bool $centered = false;
    public bool $bolded = false;
    public bool $underlined = false;
    public bool $inverted = false;
    protected string $text = '';

    public abstract function getText(): string;

    public function center() {
        $this->centered = true;
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
