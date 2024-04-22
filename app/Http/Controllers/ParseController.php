<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Parsers\HtmlParser;
use App\Parsers\PdfParser;
use App\Parsers\SunmiParser;
use App\Parsers\TemplateParser;
use Illuminate\Support\Facades\Log;
use Exception;

class ParseController extends Controller
{
    private ?Receipt $receipt = null;
    private $resultResponse;

    public function getResults(): array
    {
        if (!$this->receipt) {
            throw new Exception('No parser has run yet. ParseController->run() first');
        }

        return array(
            'receipt' => $this->receipt->id,
            'endpoint' => $this->receipt->endpoint->name,
            'message' => $this->receipt->result_message,
            'response' => $this->receipt->result_response,
        );
    }

    public function getResponse()
    {
        if (!$this->receipt) {
            throw new Exception('please ParseController->run() first');
        }

        if (is_array($this->resultResponse)) {
            return response()->json($this->resultResponse);
        }

        return $this->resultResponse;
    }


    public function prepare(Receipt $receipt)
    {
        $this->receipt = $receipt;

        Log::debug('Preparing for endpoint ' . $receipt->endpoint->name);
        $receipt->result_message = 'Prepared';
        $receipt->result_response = [
            'type' => strtolower($this->receipt->endpoint->type),
            'parser' => get_class($this->getParser()),
            'result' => url("/api/receipts/{$receipt->id}")
        ];

        $receipt->save();

        return $this;
    }

    public function run(Receipt $receipt): ?ParseController
    {
        $this->receipt = $receipt;

        if (empty($receipt->endpoint->filter_terminal) || $receipt->endpoint->filter_terminal == $receipt->order->pin_terminal_id) {

            if ($receipt->hasProducts()) {
                $parser = $this->getParser();

                try {
                    Log::debug('Parsing order for endpoint ' . $receipt->endpoint->name);
                    $parser->load($receipt->endpoint->template);

                    Log::debug('Sending parsed result to endpoint ' . $receipt->endpoint->name);
                    $this->resultResponse = $parser->run();
                    $receipt->printed++;
                    $receipt->result_message = 'Completed';
                    $receipt->result_response = [
                        'type' => $this->receipt->endpoint->type,
                        'parser' => get_class($parser),
                        'result' => $parser->runOutputIsResponse() ? "[response object]" : $this->resultResponse
                    ];
                } catch (Exception $ex) {
                    Log::debug('Failed on endpoint ' . $receipt->endpoint->name);
                    Log::debug($ex->getMessage());
                    $receipt->result_message = 'Failed';
                    $receipt->result_response = [
                        'type' => $this->receipt->endpoint->type,
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
        switch ($this->receipt->endpoint->type) {
            case 'sunmi':
                return new SunmiParser($this->receipt);
            case 'html':
                return new HtmlParser($this->receipt, public_path());
            case 'pdf':
                return new PdfParser($this->receipt, storage_path());
        }

        return null;
    }
}
