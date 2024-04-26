<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\Alignment;
use App\Helpers\ReceiptSettings;
use App\Helpers\TextMods;

class TableLine extends Line
{
    public TableCell $currentCell;
    public array $cells;
    public Alignment $alignment = Alignment::left;
    public bool $bolded = false;
    public bool $underlined = false;
    public bool $inverted = false;
    public string $width = "100%";

    public function __construct(
        ReceiptSettings $defaults,
        string $value = ''
    ) {
        parent::__construct($defaults, $value);
        $this->cells = [];
    }

    public function addCell(TableCell $col)
    {
        $this->currentCell = $col;
        $this->cells[] = $this->currentCell;
    }

    /**
     * Backward compatibility for adding a text cell
     */
    public function appendText(string $text)
    {
        if (count($this->cells) == 0) {
            $length = $this->defaults->widthCharAmount;
            $length -= 2;                                    // ' €'xxx,xx
            $length -= $this->defaults->priceCharAmount;

            $this->addCell(new TableCell($this->defaults, '', $length));
        }

        $this->currentCell->appendText($text);
    }

    /**
     * Backward compatibility for adding a price cell
     */
    public function appendPrice(float $price)
    {
        if (count($this->cells) == 1) {
            $this->addCell(new TableCell($this->defaults));
            $this->currentCell->alignment = Alignment::right;
        }

        $this->currentCell->appendText('€' .
            TextMods::pad(
                number_format($price, 2, ',', ''),
                $this->defaults->priceCharAmount,
                STR_PAD_LEFT
            )
        );
    }
}
