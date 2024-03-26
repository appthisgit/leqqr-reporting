<?php

namespace App\Parsers;

use App\Models\Receipt;
use App\Parsers\Template\Printable;
use App\Parsers\Sunmi\SunmiCloudPrinter;
use App\Parsers\Template\Lines\ImageLine;
use App\Parsers\Template\Lines\ReceiptRow;
use App\Parsers\Template\Lines\TextLine;
use Exception;
use Illuminate\Support\Facades\Storage;

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

    public function send()
    {
        if ($this->receipt->settings->singleProductTemplate) {
            $products = $this->receipt->getProductsFiltered();
            $results = [];

            foreach ($products as $product) {
                $printable = $this->parseProduct($product);

                if (!empty($printable)) {
                    $result[] = $this->print($printable, $product->amount);
                }
            }

            return $results;
        } else {
            $printable = $this->parse();
            if (!empty($printable->lines)) {
                return $this->print($printable);
            }
        }
    }

    private function setInverted(bool $inverted)
    {
        if ($this->currentInverted != $inverted)
        {
            $this->printer->setBlackWhiteReverseMode($inverted);
            $this->currentInverted = $inverted;
        }
    }

    private function setCentered(bool $center)
    {
        if ($this->currentCentered != $center) {
            $this->printer->setAlignment($center ?
                SunmiCloudPrinter::ALIGN_CENTER : SunmiCloudPrinter::ALIGN_LEFT);
            $this->currentCentered = $center;
        }
    }
    private function setBold(bool $bold)
    {
        if ($this->currentBold != $bold) {
            $this->printer->setPrintModes($bold, false, false);
            $this->currentBold = $bold;
        }
    }
    private function setUnderline(bool $underline)
    {
        if ($this->currentUnderlined != $underline) {
            $this->printer->setUnderlineMode($underline ? 2 : 0);
            $this->currentUnderlined = $underline;
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

    private function print(Printable $printable, int $amount = 1)
    {
        foreach ($printable->lines as $line) {

            if ($line->margins->top > $this->receipt->settings->lineMargins->top) {
                $this->printer->lineFeed($line->margins->top / 10);
            }

            switch (get_class($line)) {
                case TextLine::class:
                case ReceiptRow::class:

                    /** @var \App\Parsers\Template\Lines\TextLine */
                    $textLine = $line;

                    if ($textLine->margins->top > $this->receipt->settings->lineMargins->top) {
                        $this->printer->lineFeed($textLine->margins->top / 10);
                    }

                    $this->setInverted($textLine->inverted);
                    $this->setCentered($textLine->centered);
                    $this->setBold($textLine->bolded);
                    $this->setUnderline($textLine->underlined);
                    $this->setFont($textLine->font);
                    $this->setFontSize($textLine->fontSize);

                    $this->printer->appendText($textLine->getText() . "\n");

                    break;
                case ImageLine::class:
                    /** @var \App\Parsers\Template\Lines\ImageLine */
                    $imageLine = $line;

                    // $this->setCentered(true);
                    $this->printer->appendImage(Storage::path('public/' . $imageLine->image), SunmiCloudPrinter::DIFFUSE_DITHER);

                    break;
                default:
                    throw new Exception("how did you get here? >> " . get_class($line));
                    break;
            }

            if ($line->margins->bottom > $this->receipt->settings->lineMargins->bottom) {
                $this->printer->lineFeed($line->margins->bottom / 10);
            }

            // Log::debug($line);
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

        if ($succes) {
            $this->receipt->printed++;
            $this->receipt->save();
            return $this->printer->lastResult;
        }

        return $this->printer->lastError;
    }
}
