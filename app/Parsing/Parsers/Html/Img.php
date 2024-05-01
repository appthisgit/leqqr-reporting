<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\ImageLine;

class Img extends HtmlElement
{
    public function __construct(
        public ImageLine $imageLine
    ) {
        parent::__construct($imageLine);
    }

    public function getHtml(): string
    {
        return '<img' . $this->formatAttributes() . ' src="'. $this->imageLine->getImage() .'" />';
    }
}
