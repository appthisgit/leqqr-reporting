<?php

namespace App\Parsers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Endpoint;
use App\Models\Receipt;
use App\Parsers\Template\Printable;
use App\Parsers\Sunmi\SunmiCloudPrinter;
use App\Parsers\Template\Lines\ImageLine;
use App\Parsers\Template\Lines\ReceiptRow;
use App\Parsers\Template\Lines\TextLine;
use Illuminate\Support\Facades\Log;

class SunmiParser extends TemplateParser
{

    private SunmiCloudPrinter $printer;

    public function __construct(
        OrderData $order,
        CompanyData $company,
        private Endpoint $endpoint,
    ) {
        parent::__construct(
            new Receipt(
                $order,
                $company,
                $this->endpoint->filter_printable,
                $this->endpoint->filter_zone
            )
        );
        $this->printer = new SunmiCloudPrinter();
    }

    public function send()
    {
        if ($this->receipt->settings->singleProductTemplate) {

            foreach ( $this->receipt->getProductsFiltered() as $product) {
                $printable = $this->parseProduct($product);
                if (!empty($printable)) {
                    for ($i = 0; $i < $product->amount; $i++) {
                        $this->print($printable);
                    }
                }
            }
        } else {
            $printable = $this->parse();
            if (!empty($printable)) {
                                $this->print($printable);
                            }
        }
    }

    private function resetPrinter() {
        $this->printer->restoreDefaultLineSpacing();
        $this->printer->setPrintModes(false, false, false);
        $this->printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
        $this->printer->setHarfBuzzAsciiCharSize($this->receipt->settings->fontSize);
        $this->setPrinterFont($this->receipt->settings->font);
    }

    private function setPrinterFont(string $font) {
        switch ($font) {
            default:
            case 'Lucida Console':
            case 'SansSerif':
                $this->printer->selectAsciiCharFont(1);
                break;
            case 'Monospaced':
                $this->printer->selectAsciiCharFont(0);
                break;
        }
    }

    private function print(Printable $printable)
    {
        $this->resetPrinter();
        // $this->printer->lineFeed();

        foreach ($printable->lines as $line) {
            switch (get_class($line)) {
                case TextLine::class:
                case ReceiptRow::class:

                    /** @var \App\Parsers\Template\Lines\TextLine */
                    $textLine = $line;

                    if ($textLine->margins->top > $this->receipt->settings->lineMargins->top) {
                        $this->printer->lineFeed($textLine->margins->top / 10);
                    }
                    if ($textLine->centered) {
                        $this->printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
                    }
                    if ($textLine->bolded) {
                        $this->printer->setPrintModes(true, false, false);
                    }
                    if ($textLine->fontSize != $this->receipt->settings->fontSize) {
                        $this->printer->setHarfBuzzAsciiCharSize($textLine->fontSize);
                    }
                    if ($textLine->font != $this->receipt->settings->font) {
                        $this->setPrinterFont($textLine->font);
                    }


                    $this->printer->appendText($textLine->getText() . "\n");

                    $this->resetPrinter();

                    if ($textLine->margins->bottom > $this->receipt->settings->lineMargins->bottom) {
                        $this->printer->lineFeed($textLine->margins->bottom / 10);
                    }

                    break;
                case ImageLine::class:
                    /** @var \App\Parsers\Template\Lines\ImageLine */
                    $imageLine = $line;

                    if ($imageLine->margins->top > $this->receipt->settings->lineMargins->top) {
                        $this->printer->lineFeed($imageLine->margins->top / 10);
                    }

                    $this->printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
                    $this->printer->appendText("<images are not yet supported>" . "\n");

                    $this->resetPrinter();

                    if ($imageLine->margins->bottom > $this->receipt->settings->lineMargins->bottom) {
                        $this->printer->lineFeed($imageLine->margins->bottom / 10);
                    }
                    break;
                default:
                    Log::error("how did you get here? >> " . get_class($line));
                    break;
            }
        }

        $this->printer->printAndExitPageMode();
        $this->printer->lineFeed(4);
        $this->printer->cutPaper(false);
        $this->printer->pushContent(
            $this->endpoint->target, 
            sprintf("%s_%s", $this->endpoint->target, uniqid())
        );
    }
}
