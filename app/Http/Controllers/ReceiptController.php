<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Parsing\ReceiptProcessor;

class ReceiptController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function __invoke(Receipt $receipt)
    {
        $processor = new ReceiptProcessor($receipt);
        $processor->parse();
        return $processor->getResponse();
    }
}
