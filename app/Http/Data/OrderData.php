<?php

namespace App\Http\Data;

use Spatie\LaravelData\DataCollection;

class OrderData extends OrderCollectionItem
{
    /** @var QuestionData[] */
    public readonly DataCollection $questions;

    public function __construct(

        /** @var ProductData[] */
        public DataCollection $products,
        public VatData $vat,
        public readonly string $name,
        public readonly ?string $notes,
        public readonly string $order_ready,
        public readonly ?string $table_nr,
        public readonly ?string $buzzer_nr,
        public readonly string $payment_method,
        public readonly ?string $pin_transaction_receipt,
        public readonly ?string $pin_terminal_id,
        public readonly float $price_subtotal,
        public readonly float $price_total,
        public readonly ?float $price_discount,
        public readonly ?float $price_transaction,
        public readonly string $origin,
        public readonly ?string $shipment_label,
        public readonly ?string $order_locale,

        // super()
        int $id,
        string $confirmation_code,
        string $shipment_type,
        ?string $address,
        ?string $postal,
        ?string $city,
        ?string $phone,
        ?string $email,
        string $created_at,

        // optional
        ?string $questions_data = null,
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

        $reformatted = array();
        if ($questions_data) {
            foreach (json_decode($questions_data) as $object) {
                foreach ((array)$object as $key => $value) {
                    $reformatted[] = array('question' => $key, 'answer' => $value);
                }
            }
        }
        $this->questions = QuestionData::collection($reformatted);
    }

    public function getShipmentLabel(): string
    {
        return $this->shipment_label ?? __($this->shipment_type, [], $this->getLocale());
    }

    public function getLocale(): string
    {
        return $this->order_locale ?? 'nl';
    }

    public function hasPinTransactionReceipt(): bool
    {
        return !empty(trim($this->pin_transaction_receipt));
    }

    public function isIdeal(): bool
    {
        return $this->payment_method == 'ideal';
    }
}
