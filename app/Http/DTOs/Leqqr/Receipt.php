<?php

namespace App\Http\DTOs\Leqqr;

use App\Helpers\ReceiptSettings;

class Receipt
{

    private array $cachedFilteredProducts;


    public function __construct(
        public Order $order,
        public Company $company,
        public ReceiptSettings $settings = new ReceiptSettings(),
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

    //TODO: parseTemplate function?
}
