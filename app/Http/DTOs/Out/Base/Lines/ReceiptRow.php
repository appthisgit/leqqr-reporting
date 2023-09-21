<?php

namespace App\Http\DTOs\Out\Base\Lines;

class ReceiptRow extends TextLine
{

    public static function fromTextLine(TextLine $line): ReceiptRow
    {
        $row = new ReceiptRow($line->text);
        $row->bolded = $line->bolded;
        $row->font = $line->font;
        $row->fontSize = $line->fontSize;
        $row->margins->top = $line->margins->top;
        $row->margins->bottom = $line->margins->bottom;

        return $row;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
