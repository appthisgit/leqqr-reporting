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
        $this->toggleStyle($this->textLine, 'font', 'font-family');
        $this->toggleStyle($this->textLine, 'fontSize', 'font-size', 'px');

        $this->setAlignment($this->textLine->alignment);

        $this->toggleClass($this->textLine, 'bolded');
        $this->toggleClass($this->textLine, 'underlined');
        $this->toggleClass($this->textLine, 'inverted');

        return '<p' . $this->formatAttributes() . '>' . $this->textLine->text . '</p>';
    }
}
