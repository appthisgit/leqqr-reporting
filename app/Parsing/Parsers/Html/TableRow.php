<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\TableLine;

class TableRow extends TableLine
{
    use HtmlElement;

    public function __construct(
        TableLine $TableLine
    ) {
        parent::__construct($TableLine->defaults);
        $this->copyAttributes($TableLine);
    }

    public function getHtml(): string
    {
        $tds = array();
        foreach ($this->cells as $cell) {
            $tds[] = new TableData($cell, $this);
        }

        return sprintf('<tr>%s</tr>',
            implode("\r\n", $tds)
        );
    }
}
