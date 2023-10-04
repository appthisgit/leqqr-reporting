<?php

namespace App\Models;

use App\Helpers\ReceiptSettings;
use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;

class Receipt
{
    public ReceiptSettings $settings;

    public function __construct(
        public OrderData $order,
        public CompanyData $company
    ){
        $this->settings = new ReceiptSettings();
    }
}
