<?php

namespace App\Parsers\Html\Lines;

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
        return '<table>'.implode('\r\n', $this->rows).'</table>';
    }
}
