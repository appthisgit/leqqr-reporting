<?php

namespace App\Parsers;

use App\Models\Receipt;
use App\Parsers\Template\Printable;
use App\Parsers\Sunmi\SunmiCloudPrinter;

class SunmiParser extends TemplateParser
{

    private SunmiCloudPrinter $printer;

    public function __construct(
        Receipt $receipt,
        private string $serialnumber,
    ) {
        parent::__construct($receipt);
        $this->printer = new SunmiCloudPrinter();
    }

    public function print(Printable $printable)
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
        $this->printer->pushContent($this->serialnumber, sprintf("%s_%010d", $this->serialnumber, time()));
    }
}
