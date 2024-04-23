<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Parsing\ReceiptProcessor;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function __invoke(Receipt $receipt)
    {
        //TODO: is this receipt actually coming in? Fix getting result
        Log::debug($receipt);
        $processor = new ReceiptProcessor($receipt);
        $processor->parse();
        return $processor->getResponse();
    }
}
