<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\TextLine;

class Paragraph extends TextLine
{
    use HtmlElement;

    public function __construct(
        TextLine $textLine
    ) {
        parent::__construct($textLine->defaults);
        $this->copyAttributes($textLine);
    }

    public function getHtml(): string
    {
        $this->prepareAttributes();

        $this->addNonDefaultStyle('font', 'font-family');
        $this->addNonDefaultStyle('fontSize', 'font-size', 'px');

        $this->addNonDefaultClass('centered');
        $this->addNonDefaultClass('bolded');
        $this->addNonDefaultClass('underlined');
        $this->addNonDefaultClass('inverted');

        return '<p' . $this->formatAttributes() . '>' . $this->text . '</p>';
    }
}
