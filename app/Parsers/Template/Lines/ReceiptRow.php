<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\ReceiptMods;
use App\Helpers\ReceiptSettings;

class ReceiptRow extends TextLine
{

    public float $price;
    public int $widthCharAmount;
    public int $priceCharAmount;

    public function __construct(
        ReceiptSettings $defaults,
    )
    {
        parent::__construct($defaults);
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
