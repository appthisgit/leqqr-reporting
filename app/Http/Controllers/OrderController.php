<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Company;
use App\Models\Endpoint;
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
        $endpoints = Endpoint::whereCompanyId($request->company['id'])->get();
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
