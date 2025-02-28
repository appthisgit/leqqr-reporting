<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\QRLine;

class QR extends HtmlElement
{
    public function __construct(
        public QRLine $qrLine
    ) {
        parent::__construct($qrLine);
    }

    public function getHtml(): string
    {
        $this->setAlignment($this->qrLine->alignment);
        return '<p' . $this->formatAttributes() . '><img src="data:image/svg+xml;base64, '. base64_encode($this->qrLine->getSVG()) .'" /></p>';
    }
}
