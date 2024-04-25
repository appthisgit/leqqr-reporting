<?php

namespace App\Parsing\Parsers;

use App\Exceptions\TemplateException;
use App\Models\Receipt;
use App\Parsing\Parsers\Html\HorizontalRule;
use App\Parsing\Parsers\Html\Paragraph;
use App\Parsing\Parsers\Html\Img;
use App\Parsing\Parsers\Html\Table;
use App\Parsing\Parsers\Html\TableRow;
use App\Parsing\Parsers\Template\Lines\DividerLine;
use App\Parsing\Parsers\Template\Lines\ImageLine;
use App\Parsing\Parsers\Template\Lines\TableLine;
use App\Parsing\Parsers\Template\Lines\TextLine;
use Exception;

class HtmlParser extends TemplateParser
{
    private array $doc;

    public function __construct(
        Receipt $receipt,
        private $font_path
    ) {
        parent::__construct(
            $receipt
        );

        $receipt->settings->printMargins->setAll(20);
        $receipt->settings->lineMargins->top = 2;
        $receipt->settings->lineMargins->bottom = 2;
        $receipt->settings->font = 'Roboto-Mono';
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

            if (get_class($line) != TableLine::class && $lastTable != null) {
                $this->doc[] = $lastTable;
                $lastTable = null;
            }

            switch (get_class($line)) {
                case DividerLine::class:
                    $this->doc[] =  new HorizontalRule($line);
                    break;
                case TextLine::class:
                    $this->doc[] =  new Paragraph($line);
                    break;
                case ImageLine::class:
                    $this->doc[] = new Img($line);
                    break;
                case TableLine::class:
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
        if ($this->receipt->endpoint->target == '80mm') {
            $receipt_styles .= "\r\n" . sprintf('width: %sch;', $this->receipt->settings->widthCharAmount);
        }
        else { 
            //A4
            $receipt_styles .= "\r\n" . 'width: 21cm;';
        }
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

        return view('receipt', [
            'receipt_styles' => $receipt_styles,
            'line_styles' => $line_styles,
            'font_path' => $this->font_path,
            'receipt' => implode("\r\n", $this->doc)
        ])->render();
    }
}
