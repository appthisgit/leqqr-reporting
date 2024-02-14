<?php

namespace App\Helpers;

class ReceiptSettings extends PrintSettings
{
    public int $priceCharAmount = 6;
    public string $stripeChar = '-';
    public bool $singleProductTemplate = false;
    public string $sort = '';
}
