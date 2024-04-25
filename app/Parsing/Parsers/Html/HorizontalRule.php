<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\DividerLine;

class HorizontalRule extends HtmlElement
{

    public function __construct(
        DividerLine $dividerLine
    ) {
        parent::__construct($dividerLine);
    }

    public function getHtml(): string
    {
        return '<hr' . $this->formatAttributes() . '/>';
    }
}
