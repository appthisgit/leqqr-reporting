<?php

namespace App\Parsing\Parsers\Html;

class Table
{
    use HtmlElement;

    public array $rows;

    public function addRow(TableRow $row)
    {
        $this->rows[] = $row;
    }

    public function getHtml(): string
    {
        return "<table>\r\n".implode("\r\n", $this->rows)."\r\n</table>";
    }
}
