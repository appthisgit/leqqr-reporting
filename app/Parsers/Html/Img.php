<?php

namespace App\Parsers\Template\Lines;

class Div extends Element
{
    public function __construct(
        ImageLine $imageLine
    ) {
        parent::__construct($imageLine);
    }

    protected function formTag(string $formatting) {
        return '<img' . $formatting . ' src="'.$this->value.'" />';
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
