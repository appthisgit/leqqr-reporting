<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\DividerLine;

class HorizontalRule extends HtmlElement
{

    public function __construct(
        private DividerLine $dividerLine
    ) {
        parent::__construct($dividerLine);
    }

    public function getHtml(): string
    {
        if ($this->dividerLine->defaults->widthCharAmount) {
            return '<p>' . $this->dividerLine->getText() . '</p>';
        }

        return '<hr' . $this->formatAttributes() . '/>';
    }
}
