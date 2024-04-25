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
        $this->setMargins($this->tableLine);        
        $this->setAlignment($this->tableLine->alignment);
        $this->toggleClass($this->tableLine, 'bolded');
        $this->toggleClass($this->tableLine, 'underlined');
        $this->toggleClass($this->tableLine, 'inverted');

        $this->setAlignment($this->tableCell->alignment);
        $this->toggleClass($this->tableCell, 'bolded');
        $this->toggleClass($this->tableCell, 'underlined');
        $this->toggleClass($this->tableCell, 'inverted');

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
