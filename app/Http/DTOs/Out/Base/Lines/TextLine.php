<?php

namespace App\Http\DTOs\Out\Base\Lines;

class TextLine extends Line
{

    public string $font = 'Lucida Console';
    public int $fontSize = 9;
    public bool $bolded = false;

    public function __construct(
        protected string $text,
    ) {
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
