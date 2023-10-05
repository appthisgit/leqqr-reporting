<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\PrintSettings;
use App\Helpers\TextMods;

class TextLine extends Line
{

    public string $font;
    public int $fontSize;
    public bool $bolded;
    public bool $wrapped;
    protected string $text;

    public function __construct(
        PrintSettings $defaults,
    ) {
        parent::__construct($defaults);
        $this->font = $defaults->font;
        $this->fontSize = $defaults->fontSize;
        $this->bolded = $defaults->bold;
        $this->wrapped = false;
        $this->text = '';
    }

    public function getText(): string
    {
        return ($this->wrapped) ?
            TextMods::wordwrap($this->text, $this->defaults->widthCharAmount) : $this->text;
    }

    public function setBold()
    {
        $this->bolded = true;
    }

    public function setWordwrap()
    {
        $this->wrapped = true;
    }

    public function appendText(string $text)
    {
        $this->text .= $text;
    }
}
