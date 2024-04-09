<?php

namespace App\Parsers;

use App\Exceptions\TemplateException;
use App\Models\Receipt;
use App\Parsers\Html\Paragraph;
use App\Parsers\Html\Img;
use App\Parsers\Html\Table;
use App\Parsers\Html\TableRow;
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

        $receipt->settings->printMargins->setAll(20);
        $receipt->settings->lineMargins->top = 3;
        $receipt->settings->lineMargins->bottom = 3;
    }

    public function run()
    {
        if ($this->receipt->settings->singleProductTemplate) {
            throw new TemplateException('single-product-template="true"', "Not possible to use single product templates with HtmlParser", 0);
        }

        $printable = $this->parse();
        $this->doc = array();
        $lastTable = null;

        foreach ($printable->lines as $line) {

            switch (get_class($line)) {

                case TextLine::class:
                    if ($lastTable != null) {
                        $this->doc[] = $lastTable;
                        $lastTable = null;
                    }

                    $this->doc[] =  new Paragraph($line);
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

        if ($lastTable) {
            $this->doc[] = $lastTable;
        }

        $receipt_styles = '/* generated styles */';
        $receipt_styles .= "\r\n" . sprintf('width: %sch;', $this->receipt->settings->widthCharAmount);
        $receipt_styles .= "\r\n" . sprintf('font-family: %s;', $this->receipt->settings->font);
        $receipt_styles .= "\r\n" . sprintf('font-size: %spx;', $this->receipt->settings->fontSize);
        $receipt_styles .= "\r\n" . sprintf('padding-top: %spx;', $this->receipt->settings->printMargins->top);
        $receipt_styles .= "\r\n" . sprintf('padding-right: %spx;', $this->receipt->settings->printMargins->right);
        $receipt_styles .= "\r\n" . sprintf('padding-bottom: %spx;', $this->receipt->settings->printMargins->bottom);
        $receipt_styles .= "\r\n" . sprintf('padding-left: %spx;', $this->receipt->settings->printMargins->left);

        $line_styles = '/* generated styles */';
        $line_styles .= "\r\n" . sprintf('font-family: %s;', $this->receipt->settings->font);
        $line_styles .= "\r\n" . sprintf('font-size: %spx;', $this->receipt->settings->fontSize);
        $line_styles .= "\r\n" . sprintf('padding-top: %spx;', $this->receipt->settings->lineMargins->top);
        $line_styles .= "\r\n" . sprintf('padding-right: %spx;', $this->receipt->settings->lineMargins->right);
        $line_styles .= "\r\n" . sprintf('padding-bottom: %spx;', $this->receipt->settings->lineMargins->bottom);
        $line_styles .= "\r\n" . sprintf('padding-left: %spx;', $this->receipt->settings->lineMargins->left);

        $price_styles = '/* generated styles */';
        $price_styles .= "\r\n" . sprintf('width: %sch;', $this->receipt->settings->priceCharAmount + 2);

        return view('receipt', [
            'receipt_styles' => $receipt_styles,
            'line_styles' => $line_styles,
            'price_styles' => $price_styles,
            'receipt' => implode("\r\n", $this->doc)
        ])->render();
    }
}
