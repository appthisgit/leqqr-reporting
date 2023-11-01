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
    public bool $inverted;
    protected string $text;

    public function __construct(
        PrintSettings $defaults,
    ) {
        parent::__construct($defaults);
        $this->font = $defaults->font;
        $this->fontSize = $defaults->fontSize;
        $this->bolded = $defaults->bold;
        $this->wrapped = false;
        $this->inverted = false;
        $this->text = '';
    }

    public function getText(): string
    {
        if ($this->inverted && $this->defaults->fontSize == $this->fontSize) {
            return TextMods::pad($this->text, $this->defaults->widthCharAmount, $this->centered);
        }
        if ($this->wrapped && $this->defaults->fontSize == $this->fontSize) {
            return TextMods::wordwrap($this->text, $this->defaults->widthCharAmount);
        }

        return $this->text;
    }

    public function __toString()
    {
        return $this->getText();
    }

    public function setBold()
    {
        $this->bolded = true;
    }

    public function setWordwrap()
    {
        $this->wrapped = true;
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
