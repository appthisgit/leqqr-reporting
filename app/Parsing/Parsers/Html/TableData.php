<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\TableCell;
use App\Parsing\Parsers\Template\Lines\TableLine;

class TableData extends HtmlElement
{

    public function __construct(
        private TableCell $tableCell,
        private TableLine $tableLine
    ) {
        parent::__construct($tableCell);
    }

    public function getHtml(): string
    {
        $this->addNonDefaultClass($this->tableLine, 'bolded');
        $this->addNonDefaultClass($this->tableLine, 'underlined');
        $this->addNonDefaultClass($this->tableLine, 'inverted');

        if ($this->tableCell->span > 1) {
            $this->addAttribute('colspan', $this->tableCell->span);
        }

        return sprintf(
            '<td%s>%s</td>',
            $this->formatAttributes(),
            $this->tableCell->text
        );
    }
}
