<?php

namespace App\Parsing\Parsers;

use App\Models\Receipt;
use Illuminate\Support\Facades\App;

class PdfParser extends HtmlParser
{

    public function __construct(
        Receipt $receipt,
        string $font_path
    ) {
        parent::__construct(
            $receipt,
            $font_path
        );
    }

    public function run()
    { 
        $html = parent::run();

        if ($this->receipt->endpoint->target == '80mm') {
            global $bodyHeight;
            $bodyHeight = 0;

            // First run to get body height
            $pdf = App::make('dompdf.wrapper');
            $pdf->setCallbacks(
                array(
                    'myCallbacks' => array(
                        'event' => 'end_frame',
                        'f' => function ($frame) {
                            if (strtolower($frame->get_node()->nodeName) === "body") {
                                global $bodyHeight;
                                $padding_box = $frame->get_padding_box();

                                $bodyHeight = $padding_box['h'];
                            }
                        }
                    )
                )
            );
            $pdf->loadHTML($html);
            $pdf->render();
            unset($pdf);
        }

        // Second run to set the correct paper sizes
        $pdf = App::make('dompdf.wrapper');
        if ($this->receipt->endpoint->target == '80mm') {
            $pdf->setPaper([0, 0, 277, $bodyHeight]);
        }
        $pdf->loadHTML($html);
        $pdf->render();

        return $pdf->stream();
    }
}
