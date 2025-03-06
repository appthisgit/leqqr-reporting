<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\Alignment;
use App\Helpers\ReceiptSettings;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRLine extends Line
{

    public function __construct(
        ReceiptSettings $defaults,
        public string $text = '',
        public int $size = 0,
        public Alignment $alignment = Alignment::left
    ) {
        parent::__construct($defaults);
        if ($this->size < 1) {
            $this->size = $defaults->widthCharAmount * 5;
        }
    }

    public function appendText(string $text)
    {
        $this->text .= $text;
    }

    public function getSVG(): String
    {
        if ($this->size < 50) {
            $this->size *= 10;
        }
        return QrCode::size($this->size)->generate($this->text);
    }
}
