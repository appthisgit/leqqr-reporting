<?php

namespace App\Http\Controllers;

use App\Helpers\ReceiptSettings;
use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Receipt;
use App\Models\Template;
use App\Parsers\PrintableParser;

class EventController extends Controller
{

    public function printOrder(OrderData $order, CompanyData $company)
    {
        //TODO: find from company
        $template = Template::find(123);
        $filterPrintable = false;
        $filterZone = null;

        $receipt = new Receipt(
            $order,
            $company,
            new ReceiptSettings(),
            $filterPrintable,
            $filterZone,
        );

        $printables = array();

        $parser = new PrintableParser($receipt);
        $parser->load($template);

        // Settings.singleProductTemplate (from template?)
        if (false) {
            foreach ($receipt->getProductsFiltered() as $product) {
                $printable = $parser->parse($product);
                if (!empty($printable)) {
                    for ($i = 0; $i < $product->amount; $i++) {
                        $printables[] = $printable;
                    }
                }
            }
        }
        else {
            $printable = $parser->parse();
            if (!empty($printable))
            {
                $printables[] = $printable;
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
}
