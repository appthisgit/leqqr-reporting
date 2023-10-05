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
        Log::debug($request->order);

        $order = OrderData::from($request->order);
        Log::debug($order);

        $company = CompanyData::from($request->company);
        Log::debug($company);

        foreach (Endpoint::where('order_id', $order->id) as $endpoint) {

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
                    $parser->load($endpoint->template);
                    $parser->send();
                }
            }
        }


        return response()->json([
            'msg' => 'success'
        ], 200);
    }
}