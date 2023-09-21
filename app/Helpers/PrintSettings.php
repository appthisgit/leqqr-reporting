<?php

namespace App\Helpers;

class PrintSettings
{
    public array $endChars;

    public Margins $lineMargins = new Margins(2,0,0,0);
    public string $font = 'Lucida Console';
    public int $fontSize = 9;
    public bool $bold = false;
    public bool $center = false;

    public Paddings $paddings = new Paddings(0,0,0,0);
    public int $widthPaper = 217;
    public int $widthCharAmount = 46;

    public bool $copyrightFooter = true;
}
