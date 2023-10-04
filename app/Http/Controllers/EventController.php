<?php

namespace App\Http\Controllers;

use App\Helpers\ReceiptSettings;
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

            $filterPrintable = false;
            $filterZone = null;

            $receipt = new Receipt(
                $order,
                $company,
                new ReceiptSettings(),
                $filterPrintable,
                $filterZone,
            );

            switch ($endpoint->type) {
                case 'sunmi':

                    $this->sunmiPrinter($endpoint, $receipt);
                    break;
            }
        }


        // NOW WE HAVE PRINTABLES
        /**
         * Nu haalt hij de printables hier naar toe, maar dat heeft weinig nut.
         * Nog iets bedenken om dit automatisch naar SUNMI door te sturen
         */

        return response()->json([
            'msg' => 'error'
        ], 500);
    }

    private function sunmiPrinter(Endpoint $endpoint, Receipt $receipt)
    {
        $sunmi = new SunmiParser($endpoint->target);
        $parser = new PrintableParser($receipt);
        $parser->load($endpoint->template);

        // Settings.singleProductTemplate (from template?)
        if (false) {
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
