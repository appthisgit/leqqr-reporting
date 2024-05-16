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
    public function store(Request $request)
    {
        $endpoints = Endpoint::whereCompanyId($request->company['id'])->orWhereNull('company_id')->get();
        $results = array();

        if (count($endpoints)) {
            $orderData = OrderData::from($request->order);
            $companyData = CompanyData::from($request->company);

            $order = Order::firstOrNew(
                ['id' => $orderData->id],
                [
                    'id' => $orderData->id,
                    'confirmation_code' => $orderData->confirmation_code,
                    'data' => $orderData,
                ]
            );
            $order->company()->associate(Company::fromData($companyData));
            $order->save();

            foreach ($endpoints as $endpoint) {

                // Only direct proces certain types
                if (match ($endpoint->type) {
                    'pdf' => false,
                    'html' => false,
                    'sunmi' => true
                }) {
                    $results[] = $this->ProcessOrder($order, $endpoint)->getResults();
                }
            }
            Log::debug('Completed all direct parse endpoints for company ' . $companyData->id);
        }

        return response()->json($results);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order, string $format)
    {
        $endpoint = Endpoint::where('type', $format)
            ->where(function($query) use ($order) {
                $query->whereCompanyId($order->company->id);
                $query->orWhereNull('company_id');
            })
            ->orderBy('company_id', 'desc')
            ->first();

        if ($endpoint) {
            return $this->ProcessOrder($order, $endpoint)->getResponse();
        }

        return response("No endpoint has been found for this format", 404);
    }

    /**
     * Process the order, make a receipt and process it
     */
    private function ProcessOrder(Order $order, Endpoint $endpoint): ReceiptProcessor
    {
        $receipt = new Receipt([
            'status' => 'Parsing..'
        ]);
        $receipt->order()->associate($order);
        $receipt->endpoint()->associate($endpoint);
        $receipt->save();

        $processor = new ReceiptProcessor($receipt);
        $processor->parse();

        return $processor;
    }
}
