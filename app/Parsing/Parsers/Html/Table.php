<?php

namespace App\Parsing\Parsers\Html;

class Table extends HtmlElement
{
    private array $rows;

    public function __construct(
        private string $width
    ) {
        parent::__construct(null);
    }

    public function addRow(TableRow $row)
    {
        $this->rows[] = $row;
    }

    public function getHtml(): string
    {
        return "<table style=\"width: $this->width\">\r\n" . implode("\r\n", $this->rows) . "\r\n</table>";
    }
}
