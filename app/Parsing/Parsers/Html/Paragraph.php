<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\TextLine;

class Paragraph extends HtmlElement
{

    public function __construct(
        private TextLine $textLine
    ) {
        parent::__construct($textLine);
    }

    public function getHtml(): string
    {
        $this->addNonDefaultStyle($this->textLine, 'font', 'font-family');
        $this->addNonDefaultStyle($this->textLine, 'fontSize', 'font-size', 'px');

        $this->addNonDefaultClass($this->textLine, 'centered');
        $this->addNonDefaultClass($this->textLine, 'bolded');
        $this->addNonDefaultClass($this->textLine, 'underlined');
        $this->addNonDefaultClass($this->textLine, 'inverted');

        return '<p' . $this->formatAttributes() . '>' . $this->textLine->text . '</p>';
    }
}
