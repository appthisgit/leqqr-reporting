<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Endpoint;
use App\Parsers\SunmiParser;

class EventController extends Controller
{

    public function printOrder(OrderData $order, CompanyData $company)
    {
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
