<?php

namespace App\Http\Controllers;

use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Models\Company;
use App\Models\Endpoint;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function __invoke(Request $request)
    {
        $endpoints = Endpoint::where('company_id', $request->company['id'])->get();
        $results = array();

        if (count($endpoints)) {
            $parser = new ParseController();
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

                if (!$parser->runOutputIsResponse()) {
                    $parser->run($receipt);
                } else {
                    $parser->prepare($receipt);
                }

                $results[] = $parser->getResults();
            }
            Log::debug('Completed all endpoints for company ' . $companyData->id);
        }

        return response()->json($results, 200);
    }
}
