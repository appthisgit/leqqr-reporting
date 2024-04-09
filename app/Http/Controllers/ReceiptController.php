<?php

namespace App\Http\Controllers;

use App\Models\Receipt;

class ReceiptController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function __invoke(Receipt $receipt)
    {
        $parser = new ParseController();
        $parser->run($receipt);
        return $parser->getResponse();
    }
}
