<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\DividerLine;

class HorizontalRule extends DividerLine
{
    use HtmlElement;

    public function __construct(
        DividerLine $dividerLine
    ) {
        parent::__construct($dividerLine->defaults);
        $this->copyAttributes($dividerLine);
    }

    public function getHtml(): string
    {
        $this->prepareAttributes();

        return '<hr' . $this->formatAttributes() . '/>';
    }
}
