<?php

namespace App\Http\Data;

use Spatie\LaravelData\DataCollection;

class OrderData extends OrderCollectionItem
{

    public function __construct(
        int $id,
        string $confirmation_code,
        string $shipment_type,
        ?string $address,
        ?string $postal,
        ?string $city,
        ?string $phone,
        ?string $email,
        string $created_at,

        /** @var ProductData[] */
        public DataCollection $products,
        public VatData $vat,
        public readonly string $name,            // customer name
        public readonly ?string $notes,           // customer notes
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
        public readonly ?float $price_transaction,
        public readonly string $origin,
    ) {
        parent::__construct(
            $id,
            $confirmation_code,
            $shipment_type,
            $address,
            $postal,
            $city,
            $phone,
            $email,
            $created_at
        );
    }

    public function hasPinTransactionReceipt()
    {
        return !empty(trim($this->pin_transaction_receipt));
    }

    public function isIdeal()
    {
        return $this->payment_method == 'ideal';
    }
}
