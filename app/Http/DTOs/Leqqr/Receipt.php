<?php

namespace App\Http\DTOs\Leqqr;

use App\Helpers\ReceiptSettings;

class Receipt
{
    public function __construct(
        public Order $order,
        public Company $company,
        public ReceiptSettings $settings = new ReceiptSettings(),
    ){}

    //TODO: parseTemplate function?
}
