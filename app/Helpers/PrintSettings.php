<?php

namespace App\Helpers;

class PrintSettings
{
    /**
     * Margins (actually paddings) around the whole print
     */
    public Margins $printMargins;

    /**
     * Margins (in most cases actually paddings) per line
     */
    public Margins $lineMargins;

    /**
     * Default Font
     */
    public string $font = 'Lucida Console';

    /**
     * Default font size
     */
    public int $fontSize = 9;

    /**
     * The max amount of characters in a line
     */
    public int $widthCharAmount = 46;

    /**
     * Should print the copyright at the footer
     */
    public bool $copyrightFooter = true;

    public function __construct()
    {
        $this->lineMargins =  new Margins(2,);
        $this->printMargins = new Margins();
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
