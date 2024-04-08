<?php

namespace App\Parsers\Html;

use App\Parsers\Template\Lines\ReceiptRow;
use Str;

class TableRow extends ReceiptRow
{
    use HtmlElement;

    public function __construct(
        ReceiptRow $receiptRow
    ) {
        parent::__construct($receiptRow->defaults);
        $this->copyAttributes($receiptRow);
    }

    public function getHtml(): string
    {
        $this->prepareStyling();
        $styling = $this->implodeStyling();

        if (empty($this->price)) {
            return '<tr><td colspan="2"' . $styling . '>' . $this->value . '</td></tr>';
        }

        $amount = Str::padLeft(
            number_format($this->price, 2, ',', ''), 
            $this->defaults->priceCharAmount,
            ' '
        );
        return '<tr><td' . $styling . '>' . $this->value . '</></td><td' . $styling . ' class="price">€ ' . $amount . '</td></tr>';
    }
}
