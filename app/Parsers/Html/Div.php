<?php

namespace App\Parsers\Template\Lines;

class Div extends Element
{
    public function __construct(
        TextLine $textLine
    ) {
        parent::__construct($textLine);
    }

    protected function formTag(string $formatting) {
        return '<div' . $formatting . '>' . $this->value . '<div/>';
    }

    public function getHtml(): string
    {
        $this->setNonDefaultStyle('font', 'font-family');
        $this->setNonDefaultStyle('fontSize', 'font-size', 'px');

        $this->setNonDefaultClass('bolded');
        $this->setNonDefaultClass('underlined');
        $this->setNonDefaultClass('inverted');

        return parent::getHtml();
    }
}
