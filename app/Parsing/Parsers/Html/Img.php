<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\ImageLine;

class Img extends ImageLine
{
    use HtmlElement;

    public function __construct(
        ImageLine $imageLine
    ) {
        $this->copyAttributes($imageLine);
    }

    public function getHtml(): string
    {
        $this->prepareAttributes();
        $this->addNonDefaultClass('centered');

        return '<img' . $this->formatAttributes() . ' src="'. $this->getImage() .'" />';
    }
}
