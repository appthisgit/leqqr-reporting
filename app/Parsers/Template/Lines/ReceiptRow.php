<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\ReceiptMods;
use App\Helpers\ReceiptSettings;

class ReceiptRow extends TextLine
{

    public float $price;
    public int $widthCharAmount;
    public int $priceCharAmount;

    public static function fromTextLine(TextLine $line, ReceiptSettings $defaults): ReceiptRow
    {
        $row = new ReceiptRow($line->text, $defaults);

        $row->bolded = $line->bolded;
        $row->font = $line->font;
        $row->fontSize = $line->fontSize;
        $row->margins->top = $line->margins->top;
        $row->margins->bottom = $line->margins->bottom;

        return $row;
    }

    private function __construct(
        string $text,
        ReceiptSettings $defaults,
    )
    {
        parent::__construct($text, $defaults);
        $this->widthCharAmount = $defaults->widthCharAmount;
        $this->priceCharAmount = $defaults->priceCharAmount;
    }

    public function getText(): string
    {
        if (empty($this->price)) {
            return ReceiptMods::multipad($this->text, $this->widthCharAmount);
        }

        return ReceiptMods::formatPrice($this->text, $this->price, $this->widthCharAmount, $this->priceCharAmount);
    }
}
