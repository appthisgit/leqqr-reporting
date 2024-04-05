<?php

namespace App\Parsers\Html\Lines;

use App\Parsers\Template\Lines\ReceiptRow;

class TableRow extends ReceiptRow
{
    use HtmlElement;

    public function __construct(
        ReceiptRow $receiptRow
    ) {
        $this->copyAttributes($receiptRow);
    }

    public function getHtml(): string
    {
        $this->prepareStyling();
        $styling = $this->implodeStyling();

        return '<tr><td' . $styling . '>' . $this->value . '<td/><td' . $styling . '>€' . $this->price . '<td/></tr>';
    }
}
