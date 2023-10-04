<?php

namespace App\Parsers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Endpoint;
use App\Models\Receipt;
use App\Parsers\Template\Printable;
use App\Parsers\Sunmi\SunmiCloudPrinter;

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
                    $this->printer->appendText($line->text . "\n");
                    break;
                case ImageLine::class:
                    $this->printer->appendText("<images are note supported>" . "\n");
                    break;
            }
        }

        $this->printer->printAndExitPageMode();
        $this->printer->lineFeed(4);
        $this->printer->cutPaper(false);
        $this->printer->pushContent($this->endpoint->target, sprintf("%s_%010d", $this->endpoint->target, time()));
    }
}
