<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\PrintSettings;
use App\Helpers\TextMods;

class TextLine extends Line
{

    public string $font;
    public int $fontSize;
    public bool $bolded;
    public bool $underlined;
    public bool $wrapped;
    public bool $inverted;

    public function __construct(
        PrintSettings $defaults,
    ) {
        parent::__construct($defaults);
        $this->font = $defaults->font;
        $this->fontSize = $defaults->fontSize;
        $this->bolded = false;
        $this->underlined = false;
        $this->wrapped = false;
        $this->inverted = false;
        $this->value = '';
    }

    public function getText(): string
    {
        if ($this->inverted && $this->defaults->fontSize == $this->fontSize) {
            return TextMods::pad($this->value, $this->defaults->widthCharAmount, $this->centered);
        }
        if ($this->wrapped && $this->defaults->fontSize == $this->fontSize) {
            return TextMods::wordwrap($this->value, $this->defaults->widthCharAmount);
        }

        return $this->value;
    }

    public function __toString()
    {
        return $this->getText();
    }

    public function setBold()
    {
        $this->bolded = true;
    }

    public function setUnderlined()
    {
        $this->underlined = true;
    }

    public function setWordwrap()
    {
        $this->wrapped = true;
    }

    public function setInverted()
    {
        $this->inverted = true;
    }

    public function appendText(string $value)
    {
        $this->value .= $value;
    }
}
