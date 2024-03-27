<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Company;
use App\Models\Endpoint;
use App\Models\Receipt;
use App\Parsers\SunmiParser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

                $result = array(
                    'endpoint' => $endpoint->name,
                    'type' => $endpoint->type,
                );

                if (empty($endpoint->filter_terminal) || $endpoint->filter_terminal == $orderData->pin_terminal_id) {

                    $receipt = new Receipt([
                        'confirmation_code' => $orderData->confirmation_code,
                        'order_id' => $orderData->id,
                        'order' => $orderData,
                    ]);
                    $receipt->endpoint()->associate($endpoint);
                    $receipt->company()->associate(Company::fromData($companyData));
                    $receipt->save();

                    if ($receipt->hasProducts()) {
                        $parser = null;

                        switch (strtolower($endpoint->type)) {
                            case 'sunmi':
                                $parser = new SunmiParser($receipt);
                                break;
                        }

                        try {
                            Log::debug('Parsing order for endpoint ' . $endpoint->name);
                            $parser->load($endpoint->template);

                            Log::debug('Sending parsed result to endpoint ' . $endpoint->name);
                            $receipt->result_message = "Parsed template and send";
                            $receipt->result_response = $parser->send();
                        } 
                        catch (Exception $ex) {
                            Log::debug('Failed on endpoint ' . $endpoint->name);
                            Log::debug($ex->getMessage());
                            $receipt->result_message = "Exception occurred";
                            $receipt->result_response = $ex->getMessage();
                        }
                    }
                    else {
                        $receipt->result_message = "No products left to print after filtering";
                        $receipt->result_response = [
                            "filter_on_printable" => $endpoint->filter_printable,
                            "filter_on_zone" => $endpoint->filter_zone,
                        ];
                    }
                } 
                else {
                    $receipt->result_message = "Should not print on this endpoint after checking terminal";
                    $receipt->result_response = [
                        "filter_on_terminal" => $endpoint->filter_terminal,
                        "ordered with_terminal" => $orderData->pin_terminal_id,
                    ];
                    Log::debug('Filter terminal ' . $endpoint->filter_terminal . ' does not equal order ' . $orderData->pin_terminal_id . ' for endpoint ' . $endpoint->name);
                }

                $receipt->save();
                $result['message'] = $receipt->result_message;
                $result['response'] = $receipt->result_response;
                $results[] = $result;
            }
            Log::debug('Completed all endpoints for company ' . $companyData->id);
        }

        return response()->json($results, 200);
    }
}
