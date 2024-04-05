<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Company;
use App\Models\Endpoint;
use App\Models\Receipt;
use App\Parsers\HtmlParser;
use App\Parsers\SunmiParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class EventController extends Controller
{
    public function printOrder(Request $request)
    {
        $endpoints = Endpoint::where('company_id', $request->company['id'])->get();
        $results = array();

        if (count($endpoints)) {
            $orderData = OrderData::from($request->order);
            $companyData = CompanyData::from($request->company);

            foreach ($endpoints as $endpoint) {
                $receipt = new Receipt([
                    'confirmation_code' => $orderData->confirmation_code,
                    'order_id' => $orderData->id,
                    'order' => $orderData,
                ]);
                $receipt->endpoint()->associate($endpoint);
                $receipt->company()->associate(Company::fromData($companyData));
                $receipt->save();

                $results[] = $this->process($receipt);
            }
            Log::debug('Completed all endpoints for company ' . $companyData->id);
        }

        return response()->json($results, 200);
    }

    public function process(Receipt $receipt): array
    {
        $result = array(
            'endpoint' => $receipt->endpoint->name,
            'type' => $receipt->endpoint->type,
        );

        if (empty($receipt->endpoint->filter_terminal) || $receipt->endpoint->filter_terminal == $receipt->order->pin_terminal_id) {

            if ($receipt->hasProducts()) {
                $parser = null;

                switch (strtolower($receipt->endpoint->type)) {
                    case 'sunmi':
                        $parser = new SunmiParser($receipt);
                        break;
                    case 'html':
                        $parser = new HtmlParser($receipt);
                }

                try {
                    Log::debug('Parsing order for endpoint ' . $receipt->endpoint->name);
                    $parser->load($receipt->endpoint->template);

                    Log::debug('Sending parsed result to endpoint ' . $receipt->endpoint->name);
                    $receipt->result_message = "Parsed template and send";
                    $receipt->result_response = [
                        $receipt->endpoint->type . "_result" => $parser->send()
                    ];
                } catch (Exception $ex) {
                    Log::debug('Failed on endpoint ' . $receipt->endpoint->name);
                    Log::debug($ex->getMessage());
                    $receipt->result_message = "Exception";
                    $receipt->result_response = [
                        "Type" => get_class($ex),
                        "message" => $ex->getMessage(),
                    ];
                }
            } else {
                $receipt->result_message = "No products after filtering";
                $receipt->result_response = [
                    "filter_on_printable" => $receipt->endpoint->filter_printable,
                    "filter_on_zone" => $receipt->endpoint->filter_zone,
                ];
            }
        } else {
            $receipt->result_message = "Printer not for terminal";
            $receipt->result_response = [
                "filter_on_terminal" => $receipt->endpoint->filter_terminal,
                "ordered with_terminal" => $receipt->order->pin_terminal_id,
            ];
            
            Log::debug(sprintf('Filter terminal %s does not equal order %s for endpoint %s',
                $receipt->endpoint->filter_terminal,
                $receipt->order->pin_terminal_id,
                $receipt->endpoint->name
            ));
        }

        $receipt->save();
        $result['message'] = $receipt->result_message;
        $result['response'] = $receipt->result_response;

        return $result; 
    }
}
