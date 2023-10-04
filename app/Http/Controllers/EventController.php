<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Endpoint;
use App\Models\Receipt;
use App\Parsers\PrintableParser;
use App\Parsers\SunmiParser;

class EventController extends Controller
{

    public function printOrder(OrderData $order, CompanyData $company)
    {
        foreach (Endpoint::where('order_id', $order->id) as $endpoint) {

            if (empty($endpoint->filter_terminal) || $endpoint->filter_terminal == $order->pin_terminal_id) {

                $receipt = new Receipt(
                    $order,
                    $company,
                    $endpoint->filter_printable,
                    $endpoint->filter_zone,
                );

                switch ($endpoint->type) {
                    case 'sunmi':
                        $this->sunmiPrinter($endpoint, $receipt);
                        break;
                }
            }
        }

        return response()->json([
            'msg' => 'success'
        ], 200);
    }

    private function sunmiPrinter(Endpoint $endpoint, Receipt $receipt)
    {
        $sunmi = new SunmiParser($endpoint->target);
        $parser = new PrintableParser($receipt);
        $parser->load($endpoint->template);

        if ($receipt->settings->singleProductTemplate) {
            foreach ($receipt->getProductsFiltered() as $product) {
                $printable = $parser->parse($product);
                if (!empty($printable)) {
                    for ($i = 0; $i < $product->amount; $i++) {
                        $sunmi->print($printable);
                    }
                }
            }
        } else {
            $printable = $parser->parse();
            if (!empty($printable)) {
                $sunmi->print($printable);
            }
        }
    }
}
