<?php

namespace App\Parsers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Endpoint;
use App\Models\Receipt;
use App\Parsers\Template\Printable;
use App\Parsers\Sunmi\SunmiCloudPrinter;
use App\Parsers\Template\Lines\ReceiptRow;

class SunmiParser extends TemplateParser
{

    private SunmiCloudPrinter $printer;

    public function __construct(
        OrderData $order,
        CompanyData $company,
        private Endpoint $endpoint,
    ) {
        parent::__construct(
            new Receipt($order, $company)
        );
        $this->printer = new SunmiCloudPrinter();
    }

    public function send()
    {
        if ($this->receipt->settings->singleProductTemplate) {

            $filteredProducts = $this->receipt->order->getProductsFiltered(
                $this->endpoint->filter_printable,
                $this->endpoint->filter_zone
            );

            foreach ($filteredProducts as $product) {
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

    private function print(Printable $printable)
    {
        $this->printer->lineFeed();

        foreach ($printable->lines as $line) {
            switch (get_class($line)) {
                case TextLine::class:

                    /** @var \App\Parsers\Template\Lines\TextLine */
                    $textLine = $line;

                    if ($textLine->centered) {
                        $this->printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
                    }
                    if ($textLine->bolded) {
                        $this->printer->setPrintModes(true, false, false);
                    }
                    if ($textLine->fontSize != $this->receipt->settings->fontSize) {
                        //todo: make this available
                    }
                    if ($textLine->font != $this->receipt->settings->font) {
                        //todo: make this available
                    }

                    $this->printer->appendText($textLine->getText() . "\n");

                    // reset for next line
                    $this->printer->setPrintModes(false, false, false);
                    $this->printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);

                    break;
                case ReceiptRow::class:
                    /** @var \App\Parsers\Template\Lines\ReceiptRow */
                    $receiptLine = $line;

                    // $printer->setupColumns(
                    //     [96 , SunmiCloudPrinter::ALIGN_LEFT  , 0],
                    //     [144, SunmiCloudPrinter::ALIGN_CENTER, 0],
                    //     [0  , SunmiCloudPrinter::ALIGN_RIGHT , SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE]);
                    // //$printer->printInColumns("商品名称", "数量\n(单位：随意)", "小计\n(单位：元)");
                    // //$printer->lineFeed();
                    // $printer->printInColumns("Testje geprint op", date("Y-m-d h:i:sa"), "€ 1.234,00");

                    $this->printer->appendText($receiptLine->getText() . "\n");
                    break;
                case ImageLine::class:
                    /** @var \App\Parsers\Template\Lines\ImageLine */
                    $imageLine = $line;

                    $this->printer->appendText("<images are not yet supported>" . "\n");
                    break;
            }
        }

        $this->printer->printAndExitPageMode();
        $this->printer->lineFeed(4);
        $this->printer->cutPaper(false);
        $this->printer->pushContent($this->endpoint->target, sprintf("%s_%010d", $this->endpoint->target, time()));
    }
}
