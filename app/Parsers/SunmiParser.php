<?php

namespace App\Parsers;

use App\Models\Receipt;
use App\Parsers\Template\Printable;
use App\Parsers\Sunmi\SunmiCloudPrinter;
use App\Parsers\Template\Lines\ImageLine;
use App\Parsers\Template\Lines\TableCell;
use App\Parsers\Template\Lines\TableLine;
use App\Parsers\Template\Lines\TextLine;
use Exception;

class SunmiParser extends TemplateParser
{

    private SunmiCloudPrinter $printer;
    private ?bool $currentInverted;
    private ?bool $currentCentered;
    private ?bool $currentBold;
    private ?bool $currentUnderlined;
    private ?int $currentFont;
    private ?int $currentFontSize;

    public function __construct(
        Receipt $receipt
    ) {
        parent::__construct(
            $receipt
        );
        $this->printer = new SunmiCloudPrinter(500);
        $this->currentInverted = null;
        $this->currentCentered = null;
        $this->currentBold = null;
        $this->currentUnderlined = null;
        $this->currentFont = null;
        $this->currentFontSize = null;
    }

    public function run()
    {
        if ($this->receipt->settings->singleProductTemplate) {
            $products = $this->receipt->getProducts();
            $results = [];

            foreach ($products as $product) {
                $printable = $this->parseProduct($product);

                if (!empty($printable)) {
                    $results[] = $this->print($printable, $product->amount);
                }
            }

            return $results;
        }
        else {
            $printable = $this->parse();
            return $this->print($printable);
        }
    }

    private function setFont(string $font)
    {
        $selectFont = ($font == 'Monospaced') ? 0 : 1;

        if ($this->currentFont != $selectFont) {
            $this->printer->selectAsciiCharFont($selectFont);
            $this->currentFont == $selectFont;
        }
    }
    private function setFontSize(int $size)
    {
        if ($this->currentFontSize != $size) {
            $this->printer->setHarfBuzzAsciiCharSize($size);
            $this->currentFontSize = $size;
        }
    }

    private function setFormatting(TextLine|TableCell $item)
    {
        if ($this->currentInverted != $item->inverted)
        {
            $this->printer->setBlackWhiteReverseMode($item->inverted);
            $this->currentInverted = $item->inverted;
        }
        if ($this->currentCentered != $item->centered) {
            $this->printer->setAlignment($item->centered ?
                SunmiCloudPrinter::ALIGN_CENTER : SunmiCloudPrinter::ALIGN_LEFT);
            $this->currentCentered = $item->centered;
        }
        if ($this->currentBold != $item->bolded) {
            $this->printer->setPrintModes($item->bolded, false, false);
            $this->currentBold = $item->bolded;
        }
        if ($this->currentUnderlined != $item->underlined) {
            $this->printer->setUnderlineMode($item->underlined ? 2 : 0);
            $this->currentUnderlined = $item->underlined;
        }
    }

    private function print(Printable $printable, int $amount = 1)
    {
        foreach ($printable->lines as $line) {

            if ($line->margins->top > $this->receipt->settings->lineMargins->top) {
                $this->printer->lineFeed($line->margins->top / 10);
            }

            switch (get_class($line)) {
                case TextLine::class:
                    /** @var \App\Parsers\Template\Lines\TextLine */
                    $textLine = $line;

                    $this->setFormatting($textLine);
                    $this->setFont($textLine->font);
                    $this->setFontSize($textLine->fontSize);

                    // Log::debug($textLine->getText());
                    $this->printer->appendText($textLine->getText() . "\n");

                    break;
                case TableLine::class:
                    /** @var \App\Parsers\Template\Lines\TableLine */
                    $tableLine = $line;

                    $prependValue = '';
                    foreach ($tableLine->cells as $cell) {
                        $this->setFormatting($cell);
                        $this->printer->appendText($prependValue . $cell->getText());
                        $prependValue = ' ';
                    }

                    $this->printer->appendText("\n");
                    break;
                case ImageLine::class:
                    /** @var \App\Parsers\Template\Lines\ImageLine */
                    $imageLine = $line;

                    $this->printer->appendImage( $imageLine->getImage(), SunmiCloudPrinter::DIFFUSE_DITHER);

                    break;
                default:
                    throw new Exception("how did you get here? >> " . get_class($line));
                    break;
            }

            if ($line->margins->bottom > $this->receipt->settings->lineMargins->bottom) {
                $this->printer->lineFeed($line->margins->bottom / 10);
            }
        }

        $this->printer->lineFeed(4);
        $this->printer->cutPaper(false);
        $succes = $this->printer->pushContent(
            $this->receipt->endpoint->target,
            sprintf("%s_%s", $this->receipt->endpoint->target, uniqid()),
            1,
            $amount,
            'New order',
            1 // Amount of times text is said by printer
        );
        $this->printer->clear();

        if (!$succes) {
            throw new Exception($this->printer->lastError);
        }

        return $this->printer->lastResult;
    }
}
