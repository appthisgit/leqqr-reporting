<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\ReceiptSettings;

class TableLine extends Line
{
    public array $cells;

    public function __construct(
        ReceiptSettings $defaults,
        string $value = ''
    ) {
        parent::__construct($defaults, $value);
        $this->cells = [];
    }

    public function addCell(TableCell $cell)
    {
        $this->cells[] = $cell;
    }

    public function addCells(TableCell ...$cells)
    {
        array_push($this->cells, $cells);
    }

    /**
     * Backward compatibility for adding a text cell
     */
    public function appendText(string $text)
    {
        if (count($this->cells) == 0) {
            $length = $this->defaults->widthCharAmount - 1;  // 1 for white space in between cells
            $length -= 2;                                    // '€' and white space between cells
            $length -= $this->defaults->priceCharAmount;
            $this->cells[] = new TableCell('', $length);
        }

        $this->cells[0]->appendText($text);
    }

    /**
     * Backward compatibility for adding a price cell
     */
    public function appendPrice(float $price)
    {
        $this->cells[] = new TableCell('€');
        $this->cells[] = new TableCell(
            number_format($price, 2, ',', ''),
            $this->defaults->priceCharAmount,
            'left'
        );
    }

}
