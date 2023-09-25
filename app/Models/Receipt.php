<?php

namespace App\Models;

use App\Helpers\ReceiptSettings;
use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;

class Receipt
{

    private array $cachedFilteredProducts;


    public function __construct(
        public OrderData $order,
        public CompanyData $company,
        public ReceiptSettings $settings,
        private readonly bool $filterPrintable,
        private readonly ?string $filterZone,
    ){}

    public function getProductsFiltered(): array
    {
        if (empty($this->cachedFilteredProducts)) {
            $this->cachedFilteredProducts = $this->order->getProductsFiltered($this->filterPrintable, $this->filterZone);
        }
        return $this->cachedFilteredProducts;
    }

    public function parse(Template $template)
    {

    }
    //TODO: parseTemplate function?
}
