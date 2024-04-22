<?php

namespace App\Parsing;

use App\Models\Receipt;
use App\Parsing\Parsers\HtmlParser;
use App\Parsing\Parsers\PdfParser;
use App\Parsing\Parsers\SunmiParser;
use App\Parsing\Parsers\TemplateParser;
use Illuminate\Support\Facades\Log;
use Exception;

class ReceiptProcessor
{
    public TemplateParser $parser;
    private $output;

    public function __construct(
        private Receipt $receipt
    )
    {     
        switch ($this->receipt->endpoint->type) {
            case 'sunmi':
                $this->parser = new SunmiParser($this->receipt);
            case 'html':
                $this->parser =  new HtmlParser($this->receipt, public_path());
            case 'pdf':
                $this->parser =  new PdfParser($this->receipt, storage_path());
        }
    }


    public function getResults(): array
    {
        if (!$this->output) {
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
        if (!$this->output) {
            throw new Exception('please ReceiptParser->run() first');
        }

        if (is_array($this->output)) {
            return response()->json($this->output);
        }

        return $this->output;
    }


    public function prepare(): self
    {
        Log::debug('Preparing for endpoint ' . $this->receipt->endpoint->name);
        $this->output = url("/api/receipts/{$this->receipt->id}");

        $this->receipt->result_message = 'Prepared';
        $this->receipt->result_response = [
            'type' => strtolower($this->receipt->endpoint->type),
            'parser' => get_class($this->parser),
            'result' => $this->output
        ];

        $this->receipt->save();

        return $this;
    }

    public function parse(): self
    {

        if (empty($this->receipt->endpoint->filter_terminal) || $this->receipt->endpoint->filter_terminal == $this->receipt->order->pin_terminal_id) {

            if ($this->receipt->hasProducts()) {

                try {
                    Log::debug('Parsing order for endpoint ' . $this->receipt->endpoint->name);
                    $this->parser->load($this->receipt->endpoint->template);

                    Log::debug('Sending parsed result to endpoint ' . $this->receipt->endpoint->name);
                    $this->output = $this->parser->run();
                    $this->receipt->printed++;
                    $this->receipt->result_message = 'Completed';
                    $this->receipt->result_response = [
                        'type' => $this->receipt->endpoint->type,
                        'parser' => get_class($this->parser),
                        'result' => $this->parser->runOutputIsResponse() ? "[response object]" : $this->output 
                    ];
                } catch (Exception $ex) {
                    Log::debug('Failed on endpoint ' . $this->receipt->endpoint->name);
                    Log::debug($ex->getMessage());
                    $this->receipt->result_message = 'Failed';
                    $this->receipt->result_response = [
                        'type' => $this->receipt->endpoint->type,
                        'parser' => get_class($this->parser),
                        'result' => [
                            'Exception' => get_class($ex),
                            'message' => $ex->getMessage(),
                        ]
                    ];
                }
            } else {
                $this->receipt->result_message = 'No products after filtering';
                $this->receipt->result_response = [
                    'result' => [
                        'filter_on_printable' => $this->receipt->endpoint->filter_printable,
                        'filter_on_zone' => $this->receipt->endpoint->filter_zone,
                    ]
                ];
            }
        } else {
            $this->receipt->result_message = 'Should not print on this device';
            $this->receipt->result_response = [
                'result' => [
                    'filter_on_terminal' => $this->receipt->endpoint->filter_terminal,
                    'ordered with_terminal' => $this->receipt->order->pin_terminal_id,
                ]
            ];

            Log::debug(sprintf(
                'Filter terminal %s does not equal order %s for endpoint %s',
                $this->receipt->endpoint->filter_terminal,
                $this->receipt->order->pin_terminal_id,
                $this->receipt->endpoint->name
            ));
        }

        $this->receipt->save();

        return $this;
    }

}
