<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Company;
use App\Models\Endpoint;
use App\Models\Order;
use App\Models\Receipt;
use App\Parsing\ReceiptProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function __invoke(Request $request)
    {
        $endpoints = Endpoint::whereCompanyId($request->company['id'])->orWhereNull('company_id')->get();
        $results = array();

        if (count($endpoints)) {
            $orderData = OrderData::from($request->order);
            $companyData = CompanyData::from($request->company);

            $order = new Order([
                'id' => $orderData->id,
                'confirmation_code' => $orderData->confirmation_code,
                'data' => $orderData,
            ]);
            $order->company()->associate(Company::fromData($companyData));
            $order->save();

            foreach ($endpoints as $endpoint) {
                $receipt = new Receipt();
                $receipt->order()->associate($order);
                $receipt->endpoint()->associate($endpoint);
                $receipt->save();

                $processor = new ReceiptProcessor($receipt);

                if (!$processor->parser->runOutputIsResponse()) {
                    $processor->parse();
                } else {
                    $processor->prepare();
                }

                $results[] = $processor->getResults();
            }
            Log::debug('Completed all endpoints for company ' . $companyData->id);
        }

        return response()->json($results, 200);
    }
}
