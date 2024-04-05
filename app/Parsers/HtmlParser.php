<?php

namespace App\Parsers;

use App\Models\Receipt;
use App\Parsers\Html\Lines\Div;
use App\Parsers\Html\Lines\Img;
use App\Parsers\Html\Lines\Table;
use App\Parsers\Html\Lines\TableRow;
use App\Parsers\Template\Printable;
use App\Parsers\Template\Lines\ImageLine;
use App\Parsers\Template\Lines\ReceiptRow;
use App\Parsers\Template\Lines\TextLine;
use Exception;

class HtmlParser extends TemplateParser
{

    private array $doc;

    public function __construct(
        Receipt $receipt
    ) {
        parent::__construct(
            $receipt
        );
    }

    public function send()
    {
        if ($this->receipt->settings->singleProductTemplate) {
            $results = [];

            foreach ($this->receipt->getProducts() as $product) {
                $results[] = $this->print(
                    $this->parseProduct($product),
                    $product->amount
                );
            }

            return $results;
        } else {
            return $this->print($this->parse());
        }
    }



    private function print(Printable $printable, int $amount = 1)
    {
        $this->doc = array();
        $lastTable = null;

        foreach ($printable->lines as $line) {

            switch (get_class($line)) {

                case TextLine::class:
                    if ($lastTable != null) {
                        $this->doc[] = $lastTable;
                        $lastTable = null;
                    }

                    $this->doc[] =  new Div($line);
                    break;

                case ImageLine::class:
                    if ($lastTable != null) {
                        $this->doc[] = $lastTable;
                        $lastTable = null;
                    }

                    $this->doc[] = new Img($line);
                    break;
                case ReceiptRow::class:
                    if ($lastTable == null) {
                        $lastTable = new Table();
                    }

                    $lastTable->addRow(new TableRow($line));
                    break;
                default:
                    throw new Exception("how did you get here? >> " . get_class($line));
                    break;
            }
        }

        return implode('\r\n', $this->doc);
    }
}
