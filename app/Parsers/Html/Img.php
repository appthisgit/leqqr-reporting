<?php

namespace App\Parsers\Template\Lines;

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

        return '<img' . $this->implodeStyling() . ' src="'. $this->getImage() .'" />';
    }
}
