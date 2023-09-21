<?php

namespace App\Http\DTOs\Leqqr;

class Order extends OrderCollectionItem
{

    public function __construct(
        public array $products,
        public array $taxes,
        public string $name,            // customer name
        public string $notes,           // customer notes
        public string $coupon_code,
        public string $order_ready,
        public string $table_nr,
        public ?string $buzzer_nr,
        public string $payment_method,
        public string $pin_transaction_receipt,
        public string $pin_terminal_id,
        public string $mollie_payment_method,
        public float $price_subtotal,
        public float $price_total,
        public ?float $price_discount,
        public ?float $price_tax,
        public ?float $price_delivery,
        public ?float $price_transaction,
    ) {
    }


    public function hasPinTransactionReceipt()
    {
        return !empty(trim($this->pin_transaction_receipt));
    }

    public function isIdeal()
    {
        return $this->payment_method == 'ideal';
    }

    public function getProductsFiltered(bool $filterPrintable, ?string $filterZone): array
    {
        if ($filterPrintable || $filterZone) {
            $filteredProducts = array();

            foreach ($this->products as $product) {
                if ($product->inFilters($filterPrintable, $filterZone)) {
                    $filteredProducts[] = $product;
                }
            }

            return $filteredProducts;
        }

        return $this->products;
    }
}
