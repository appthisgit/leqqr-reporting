<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
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
        return response('', 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(Receipt $receipt)
    {
        $parser = new ParseController();
        $parser->run($receipt);
        return $parser->getResponse();
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
