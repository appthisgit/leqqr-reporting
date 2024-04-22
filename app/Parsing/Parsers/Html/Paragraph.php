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
        $this->prepareStyling();

        $this->setNonDefaultStyle('font', 'font-family');
        $this->setNonDefaultStyle('fontSize', 'font-size', 'px');

        $this->setNonDefaultClass('centered');
        $this->setNonDefaultClass('bolded');
        $this->setNonDefaultClass('underlined');
        $this->setNonDefaultClass('inverted');

        return '<p' . $this->implodeStyling() . '>' . $this->text . '</p>';
    }
}
