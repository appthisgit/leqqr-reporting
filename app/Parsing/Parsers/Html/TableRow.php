<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\TableLine;

class TableRow extends HtmlElement
{
    public function __construct(
        private TableLine $TableLine
    ) {
        parent::__construct($TableLine);
    }

    public function getHtml(): string
    {
        $tds = array();
        foreach ($this->TableLine->cells as $cell) {
            $tds[] = new TableData($cell, $this->TableLine);
        }

        return sprintf('<tr>%s</tr>',
            implode("\r\n", $tds)
        );
    }
}
