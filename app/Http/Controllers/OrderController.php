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
     * Display a listing of the resource.
     */
    public function index()
    {
        return response('', 403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

                if (strtolower($receipt->endpoint->type) == 'sunmi') {
                    $parser->run($receipt);
                }
                else {
                    $parser->prepare($receipt);
                }

                $results[] = $parser->getResults();
            }
            Log::debug('Completed all endpoints for company ' . $companyData->id);
        }

        return response()->json($results, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Receipt $receipt)
    {
        return response('', 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receipt $receipt)
    {
        return response('', 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receipt $receipt)
    {
        return response('', 403);
    }
}
