<?php

namespace App\Models;

use App\Helpers\ReceiptSettings;
use App\Http\Data\CompanyData;
use App\Http\Data\OrderData;
use App\Http\Data\ProductData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;

//TODO: update name and make into Eloquent Model and store them for history

class Receipt
{
    public ReceiptSettings $settings;

    public function __construct(
        public OrderData $order,
        public CompanyData $company,
        public bool $filter_printable,
        public ?string $filter_zone,
    ) {
        $this->settings = new ReceiptSettings();
    }

    public function getRequiredProducts(): array
    {
        $productsCollection = $this->order->products->toCollection()->filter(function (ProductData $product) {
            if (!($product->category)) {
                if ($this->filter_printable || $this->filter_zone) {
                    return $product->inFilters($this->filter_printable, $this->filter_zone);
                }

                return true;
            }

            return false;
        });

        return $productsCollection->all();
    }

    public function getProductsFiltered(): array
    {
        $productsCollection = $this->order->products->toCollection()->filter(function (ProductData $product) {
            if ($product->category) {
                if ($this->filter_printable || $this->filter_zone) {
                    return $product->inFilters($this->filter_printable, $this->filter_zone);
                }

                return true;
            }

            return false;
        });

        if (!empty($this->settings->sort)) {

            $callback = null;

            switch ($this->settings->sort) {
                case 'category-order':
                    $callback = fn (ProductData $a, ProductData $b) => $a->category?->ordernum <=> $b->category?->ordernum;
                    break;
                case 'category-order-reverse':
                    $callback = fn (ProductData $a, ProductData $b) => $b->category?->ordernum <=> $a->category?->ordernum;
                    break;
                case 'category-name':
                    $callback = fn (ProductData $a, ProductData $b) => $a->category?->name <=> $b->category?->name;
                    break;
                case 'category-name-reverse':
                    $callback = fn (ProductData $a, ProductData $b) => $b->category?->name <=> $a->category?->name;
                    break;
                case 'product-name':
                    $callback = fn (ProductData $a, ProductData $b) => $a->name <=> $b->name;
                    break;
                case 'product-name-reverse':
                    $callback = fn (ProductData $a, ProductData $b) => $b->name <=> $a->name;
                    break;
            }

            if ($callback) {
                $productsCollection->sort($callback);
            } elseif ($this->settings->sort == 'reverse') {
                $productsCollection->reverse();
            }
        }

        return $productsCollection->all();
    }
}
