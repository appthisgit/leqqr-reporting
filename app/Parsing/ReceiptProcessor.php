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
    ) {
        $this->parser = match ($this->receipt->endpoint->type) {
            'sunmi' => new SunmiParser($this->receipt),
            'html' => new HtmlParser($this->receipt, ''),
            'pdf' => new PdfParser($this->receipt, storage_path()),
        };
    }


    public function getResults(): array
    {
        if (!$this->output) {
            throw new Exception('No parser has run yet. ParseController->parse() first');
        }

        return array(
            'receipt' => $this->receipt->id,
            'endpoint' => $this->receipt->endpoint->name,
            'status' => $this->receipt->status,
            'response' => $this->receipt->response,
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

        $this->receipt->status = 'Prepared';
        $this->receipt->response = [
            'parser' => get_class($this->parser),
            'result' => $this->output
        ];

        $this->receipt->save();

        return $this;
    }

    public function parse(): self
    {

        if (empty($this->receipt->endpoint->filter_terminal) || $this->receipt->endpoint->filter_terminal == $this->receipt->order->data->pin_terminal_id) {

            if ($this->receipt->hasProducts()) {

                try {
                    Log::debug('Parsing order for endpoint ' . $this->receipt->endpoint->name);
                    $this->parser->load($this->receipt->endpoint->template);

                    Log::debug('Sending parsed result to endpoint ' . $this->receipt->endpoint->name);
                    $this->output = $this->parser->run();
                    $this->receipt->status = 'Completed';
                    $this->receipt->response = [
                        'parser' => get_class($this->parser),
                        'result' => is_array($this->output) ? $this->output : "[object]"
                    ];
                } catch (Exception $ex) {
                    Log::debug('Failed on endpoint ' . $this->receipt->endpoint->name);
                    $this->output = [
                        'Exception' => get_class($ex),
                        'message' => $ex->getMessage(),
                        'file' => $ex->getFile(),
                        'line' => $ex->getLine(),
                    ];
                    $this->receipt->status = 'Failed';
                    $this->receipt->response = [
                        'parser' => get_class($this->parser),
                        'result' => $this->output
                    ];
                }
            } else {
                $this->receipt->status = 'Done';
                $this->receipt->response = [
                    'result' => [
                        'filter_on_printable' => $this->receipt->endpoint->filter_printable,
                        'filter_on_zone' => $this->receipt->endpoint->filter_zone,
                    ]
                ];
            }
        } else {
            $this->receipt->status = 'Done';
            $this->receipt->response = [
                'result' => [
                    'filter_on_terminal' => $this->receipt->endpoint->filter_terminal,
                    'ordered with_terminal' => $this->receipt->order->data->pin_terminal_id,
                ]
            ];

            Log::debug(sprintf(
                'Filter terminal %s does not equal order %s for endpoint %s',
                $this->receipt->endpoint->filter_terminal,
                $this->receipt->order->data->pin_terminal_id,
                $this->receipt->endpoint->name
            ));
        }

        $this->receipt->save();

        return $this;
    }
}
