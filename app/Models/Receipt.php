<?php

namespace App\Models;

use App\Helpers\ReceiptSettings;
use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use Spatie\LaravelData\DataCollection;

//TODO: update name and make into Eloquent Model and store them for history

class Receipt //QueueObject?
{
    public ReceiptSettings $settings;

    public function __construct(
        public OrderData $order,
        public CompanyData $company,
        public bool $filter_printable,
        public ?string $filter_zone,
    ){
        $this->settings = new ReceiptSettings();
    }

    public function getProductsFiltered(): array|DataCollection
    {
        if ($this->filter_printable || $this->filter_zone) {
            $filteredProducts = array();

            foreach ($this->order->products as $product) {
                if ($product->inFilters($this->filter_printable, $this->filter_zone)) {
                    $filteredProducts[] = $product;
                }
            }

            return $filteredProducts;
        }

        return $this->order->products;
    }
}
