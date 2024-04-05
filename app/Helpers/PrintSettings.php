<?php

namespace App\Helpers;

class PrintSettings
{
    public Margins $lineMargins;
    public Paddings $paddings;

    public string $font = 'Lucida Console';
    public int $fontSize = 9;

    public int $widthPaper = 217;
    public int $widthCharAmount = 46;

    public bool $copyrightFooter = true;

    public function __construct()
    {
        $this->lineMargins =  new Margins(2, 0, 0, 0);
        $this->paddings = new Paddings(0, 0, 0, 0);
    }
}
