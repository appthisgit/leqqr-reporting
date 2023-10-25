<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Endpoint;
use App\Parsers\SunmiParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{

    public function printOrder(Request $request)
    {
        $endpoints = Endpoint::where('company_id', $request->company['id'])->get();

        if (count($endpoints)) {
            $order = OrderData::from($request->order);
            $company = CompanyData::from($request->company);

            foreach ($endpoints as $endpoint) {
                if (empty($endpoint->filter_terminal) || $endpoint->filter_terminal == $order->pin_terminal_id) {

                    $parser = null;

                    switch (strtolower($endpoint->type)) {
                        case 'sunmi':
                            $parser = new SunmiParser(
                                $order,
                                $company,
                                $endpoint
                            );
                            break;
                    }

                    if ($parser) {
                        Log::debug('Parsing order for endpoint ' . $endpoint->name);
                        $parser->load($endpoint->template);

                        Log::debug('Sending parsed result to endpoint ' . $endpoint->name);
                        $parser->send();
                    }
                } else {
                    Log::debug('Filter terminal ' . $endpoint->filter_terminal . ' does not equal order ' . $order->pin_terminal_id . ' for endpoint ' . $endpoint->name);
                }
            }
            Log::debug('Completed all endpoints for company ' . $company->id);
        }

        return response()->json([
            'msg' => 'success'
        ], 200);
    }
}
