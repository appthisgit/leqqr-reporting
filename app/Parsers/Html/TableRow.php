<?php

namespace App\Parsers\Html;

use App\Parsers\Template\Lines\ReceiptRow;

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
            return '<tr><td' . $styling . '>' . $this->value . '<td/><td' . $styling . '><td/></tr>';
        }

        return '<tr><td' . $styling . '>' . $this->value . '<td/><td' . $styling . '>€' . $this->price . '<td/></tr>';
    }
}
