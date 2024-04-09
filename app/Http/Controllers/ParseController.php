<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Parsers\HtmlParser;
use App\Parsers\SunmiParser;
use App\Parsers\TemplateParser;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\App;

class ParseController extends Controller
{
    private ?Receipt $lastReceipt = null;

    public function getResults(): array
    {
        if (!$this->lastReceipt) {
            throw new Exception('No parser has run yet. ParseController->run() first');
        }

        return array(
            'receipt' => $this->lastReceipt->id,
            'endpoint' => $this->lastReceipt->endpoint->name,
            // 'type' => $this->lastReceipt->endpoint->type,
            'message' => $this->lastReceipt->result_message,
            'response' => $this->lastReceipt->result_response,
        );
    }

    public function getResponse()
    {
        if (!$this->lastReceipt) {
            throw new Exception('please ParseController->run() first');
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper(array(0, 0, 164.44, 842.07), 'portrait');

        $GLOBALS['bodyHeight'] = 0;
        $GLOBALS['bodyWidth'] = 0;

        $pdf->setCallbacks(
            array(
                'myCallbacks' => array(
                    'event' => 'end_frame',
                    'f' => function ($frame) {
                        if (strtolower($frame->get_node()->nodeName) === "body") {
                            $padding_box = $frame->get_padding_box();
                            $GLOBALS['bodyHeight'] = $padding_box['h'];
                            $GLOBALS['bodyWidth'] = $padding_box['w'];
                        }
                    }
                )
            )
        );

        $pdf->loadHTML($this->lastReceipt->result_response['result']);
        $pdf->render();
        unset($pdf);

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper([0,0,$GLOBALS['bodyWidth'], $GLOBALS['bodyHeight']]);
        $pdf->loadHTML($this->lastReceipt->result_response['result']);
        $pdf->setOptions(array(
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true
        ));
        $pdf->render();
        return $pdf->stream();
    }

    public function prepare(Receipt $receipt)
    {
        $this->lastReceipt = $receipt;

        Log::debug('Preparing for endpoint ' . $receipt->endpoint->name);
        $receipt->result_message = 'Prepared';
        $receipt->result_response = [
            'parser' => get_class($this->getParser()),
            'result' => url("/api/receipts/{$receipt->id}")
        ];

        $receipt->save();

        return $this;
    }

    public function run(Receipt $receipt): ?ParseController
    {
        $this->lastReceipt = $receipt;

        if (empty($receipt->endpoint->filter_terminal) || $receipt->endpoint->filter_terminal == $receipt->order->pin_terminal_id) {

            if ($receipt->hasProducts()) {
                $parser = $this->getParser();

                try {
                    Log::debug('Parsing order for endpoint ' . $receipt->endpoint->name);
                    $parser->load($receipt->endpoint->template);

                    Log::debug('Sending parsed result to endpoint ' . $receipt->endpoint->name);
                    $receipt->printed++;
                    $receipt->result_message = 'Completed';
                    $receipt->result_response = [
                        'parser' => get_class($parser),
                        'result' => $parser->run()
                    ];
                } catch (Exception $ex) {
                    Log::debug('Failed on endpoint ' . $receipt->endpoint->name);
                    Log::debug($ex->getMessage());
                    $receipt->result_message = 'Failed';
                    $receipt->result_response = [
                        'parser' => get_class($parser),
                        'result' => [
                            'Exception' => get_class($ex),
                            'message' => $ex->getMessage(),
                        ]
                    ];
                }
            } else {
                $receipt->result_message = 'No products after filtering';
                $receipt->result_response = [
                    'result' => [
                        'filter_on_printable' => $receipt->endpoint->filter_printable,
                        'filter_on_zone' => $receipt->endpoint->filter_zone,
                    ]
                ];
            }
        } else {
            $receipt->result_message = 'Should not print on this device';
            $receipt->result_response = [
                'result' => [
                    'filter_on_terminal' => $receipt->endpoint->filter_terminal,
                    'ordered with_terminal' => $receipt->order->pin_terminal_id,
                ]
            ];

            Log::debug(sprintf(
                'Filter terminal %s does not equal order %s for endpoint %s',
                $receipt->endpoint->filter_terminal,
                $receipt->order->pin_terminal_id,
                $receipt->endpoint->name
            ));
        }

        $receipt->save();

        return $this;
    }

    private function getParser(): ?TemplateParser
    {
        switch (strtolower($this->lastReceipt->endpoint->type)) {
            case 'sunmi':
                return new SunmiParser($this->lastReceipt);
            case 'html':
                return new HtmlParser($this->lastReceipt);
        }

        return null;
    }
}
