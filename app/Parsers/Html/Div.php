<?php

namespace App\Parsers\Template\Lines;

class Div extends TextLine
{
    use HtmlElement;

    public function __construct(
        TextLine $textLine
    ) {
        $this->copyAttributes($textLine);
    }

    public function getHtml(): string
    {
        $this->prepareStyling();

        $this->setNonDefaultStyle('font', 'font-family');
        $this->setNonDefaultStyle('fontSize', 'font-size', 'px');

        $this->setNonDefaultClass('bolded');
        $this->setNonDefaultClass('underlined');
        $this->setNonDefaultClass('inverted');

        return '<div' . $this->implodeStyling() . '>' . $this->value . '<div/>';
    }
}
