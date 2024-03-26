<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Company;
use App\Models\Endpoint;
use App\Models\Receipt;
use App\Parsers\SunmiParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{

    public function printOrder(Request $request)
    {
        $endpoints = Endpoint::where('company_id', $request->company['id'])->get();

        if (count($endpoints)) {
            $orderData = OrderData::from($request->order);
            $companyData = CompanyData::from($request->company);

            foreach ($endpoints as $endpoint) {
                if (empty($endpoint->filter_terminal) || $endpoint->filter_terminal == $orderData->pin_terminal_id) {

                    $receipt = new Receipt([
                        'order' => $orderData,
                    ]);
                    $receipt->endpoint()->associate($endpoint);
                    $receipt->company()->associate(Company::fromData($companyData));
                    $receipt->save();

                    $parser = null;
                    switch (strtolower($endpoint->type)) {
                        case 'sunmi':
                            $parser = new SunmiParser($receipt);
                            break;
                    }

                    if ($parser) {
                        Log::debug('Parsing order for endpoint ' . $endpoint->name);
                        $parser->load($endpoint->template);

                        Log::debug('Sending parsed result to endpoint ' . $endpoint->name);
                        $parser->send();
                    }
                } else {
                    Log::debug('Filter terminal ' . $endpoint->filter_terminal . ' does not equal order ' . $orderData->pin_terminal_id . ' for endpoint ' . $endpoint->name);
                }
            }
            Log::debug('Completed all endpoints for company ' . $companyData->id);
        }

        return response()->json([
            'msg' => 'success'
        ], 200);
    }
}
