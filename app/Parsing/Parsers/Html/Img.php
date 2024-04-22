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
        $this->prepareStyling();
        $this->setNonDefaultClass('centered');

        return '<img' . $this->implodeStyling() . ' src="'. $this->getImage() .'" />';
    }
}
