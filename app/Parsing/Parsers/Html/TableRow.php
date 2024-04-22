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
        $this->prepareStyling();

        $tds = '';
        $prependValue = '';
        foreach ($this->cells as $cell) {
            $tds .= '<td>'. $prependValue . $cell->getText() . '</td>';

            // $this->setBold($cell->bolded);
            // $this->setUnderline($cell->underlined);
        }

        return sprintf('<tr%s>%s</tr>',
            $this->implodeStyling(),
            $tds
        );
    }
}
