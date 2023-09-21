<?php

namespace App\Http\DTOs\Leqqr;

class Order extends OrderCollectionItem
{

    public function __construct(
        public array $products,
        public array $taxes,
        public readonly string $name,            // customer name
        public readonly ?string $notes,           // customer notes
        public readonly ?string $coupon_code,
        public readonly string $order_ready,
        public readonly ?string $table_nr,
        public readonly ?string $buzzer_nr,
        public readonly string $payment_method,
        public readonly string $pin_transaction_receipt,
        public readonly ?string $pin_terminal_id,
        public readonly ?string $mollie_payment_method,
        public readonly float $price_subtotal,
        public readonly float $price_total,
        public readonly ?float $price_discount,
        public readonly ?float $price_tax,
        public readonly ?float $price_delivery,
        public readonly ?float $price_transaction,
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
