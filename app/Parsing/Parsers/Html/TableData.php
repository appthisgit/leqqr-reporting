<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\TableCell;
use Illuminate\Support\Facades\Log;

class TableData extends TableCell
{
    use HtmlElement;

    public function __construct(
        TableCell $tableCell,
        TableRow $parent
    ) {
        parent::__construct(
            $tableCell->defaults,
            $tableCell->text,
            $tableCell->maxLength,
            $tableCell->pad_type,
        );
        Log::debug($parent->bolded);
        $this->copyAttributes($parent);
        $this->copyAttributes($tableCell);
    }

    public function getHtml(): string
    {
        $this->prepareAttributes();

        $this->addNonDefaultClass('bolded');
        $this->addNonDefaultClass('underlined');
        $this->addNonDefaultClass('inverted');

        if ($this->span > 1) {
            $this->addAttribute('colspan', $this->span);
        }

        return sprintf(
            '<td%s>%s</td>',
            $this->formatAttributes(),
            $this->text
        );
    }
}
