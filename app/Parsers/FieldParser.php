<?php

namespace App\Parsers;

use App\Exceptions\TemplateException;
use App\Helpers\Strings;
use App\Http\Data\CategoryData;
use App\Http\Data\ProductData;
use App\Http\Data\QuestionData;
use App\Http\Data\VatRowData;
use App\Http\Data\VariationData;
use App\Http\Data\VariationValueData;
use App\Models\Receipt;

class FieldParser
{
    protected ?CategoryData $lastCategory;
    protected ProductData $currentProduct;
    protected VatRowData $currentVatRow;
    protected VariationData $currentVariation;
    protected VariationValueData $currentVariationValue;
    protected QuestionData $currentQuestion;

    public function __construct(
        protected readonly Receipt $receipt,
    ) {
    }

    protected function doIf(string $key, ?string $value): bool
    {
        switch ($key) {
            case 'is_delivery':
                return $this->receipt->order->isDelivery();
            case 'is_pickup':
                return $this->receipt->order->isPickup();
            case 'is_eat_in':
                return !$this->receipt->order->isDelivery() && !$this->receipt->order->isPickup();
            case 'has_tablenumer':
                return !Strings::isEmptyOrValueNull($this->receipt->order->table_nr);
            case 'has_buzzer':
                return !Strings::isEmptyOrValueNull($this->receipt->order->buzzer_nr);
            case 'has_customer_name':
                return !Strings::isEmptyOrValueNull($this->receipt->order->name);
            case 'has_customer_phone':
                return !Strings::isEmptyOrValueNull($this->receipt->order->phone);
            case 'has_customer_email':
                return !Strings::isEmptyOrValueNull($this->receipt->order->email);
            case 'has_notes':
                return !Strings::isEmptyOrValueNull($this->receipt->order->notes);
            case 'has_transaction_costs':
                return !empty($this->receipt->order->price_transaction);
            case 'has_discounts':
                return !empty($this->receipt->order->price_discount);
            case 'has_taxes':
                return !empty($this->receipt->order->vat->order_vat > 0);
            case 'is_method_cash':
                return $this->receipt->order->payment_method == 'cash';
            case 'is_method_account':
                return $this->receipt->order->payment_method == 'account';
            case 'is_method_online':
                return $this->receipt->order->payment_method == 'ideal' || $this->receipt->order->payment_method == 'online';
            case 'is_method_pin':
                return $this->receipt->order->payment_method == 'pin';
            case 'is_method_cikam':
                return $this->receipt->order->payment_method == 'cikam';
            case 'is_origin_kiosk':
                return $this->receipt->order->origin == 'kiosk';
            case 'is_origin_online':
                return $this->receipt->order->origin == 'online';
            case 'is_method_other':
                return $this->receipt->order->payment_method != 'cash'
                    && $this->receipt->order->payment_method != 'account'
                    && $this->receipt->order->payment_method != 'ideal'
                    && $this->receipt->order->payment_method != 'online'
                    && $this->receipt->order->payment_method != 'pin'
                    && $this->receipt->order->payment_method != 'cikam';
            case 'subtotal_is_different_than_total':
                return $this->receipt->order->price_total > $this->receipt->order->price_subtotal;
            case 'is_pin_terminal_id':
                $this->checkValue($value, "if key=\"$key\"", 'doesn\'t have a value!');
                return $this->receipt->order->pin_terminal_id == $value;

                // Product
            case 'no_product_kitchen_info':
                $this->checkValue($this->currentProduct, "if key=\"$key\"",  'can\'t be accessed outside of product loop');
                return Strings::isEmptyOrValueNull($this->currentProduct->kitchen_info);
            case 'has_product_kitchen_info':
                $this->checkValue($this->currentProduct, "if key=\"$key\"",  'can\'t be accessed outside of product loop');
                return Strings::isNotEmptyOrValueNull($this->currentProduct->kitchen_info);
            case 'has_product_tax':
                $this->checkValue($this->currentProduct, "if key=\"$key\"",  'can\'t be accessed outside of product loop');
                return $this->currentProduct->hasTax();
            case 'has_product_notes':
                $this->checkValue($this->currentProduct, "if key=\"$key\"",  'can\'t be accessed outside of product loop');
                return Strings::isNotEmptyOrValueNull($this->currentProduct->notes);
            case 'different_category':
                $this->checkValue($this->currentProduct, "if key=\"$key\"",  'can\'t be accessed outside of product loop');
                return empty($this->lastCategory) || $this->lastCategory->name != $this->currentProduct->category->name;

                // Variation
            case 'has_variation_price':
                $this->checkValue($this->currentVariation, "if key=\"$key\"",  'can\'t be accessed outside of variation loop');
                return $this->currentVariationValue->price > 0;
            case 'no_variation_kitchen_info':
                $this->checkValue($this->currentVariation, "if key=\"$key\"",  'can\'t be accessed outside of variation loop');
                return Strings::isEmptyOrValueNull($this->currentVariationValue->kitchen_info);
            case 'has_variation_kitchen_info':
                $this->checkValue($this->currentVariation, "if key=\"$key\"",  'can\'t be accessed outside of variation loop');
                return Strings::isNotEmptyOrValueNull($this->currentVariationValue->kitchen_info);

                // Question
            case 'has_questions':
                return count($this->receipt->order->questions);
            case 'no_question_answer':
                $this->checkValue($this->currentQuestion, "if key=\"$key\"",  'can\'t be accessed outside of variation loop');
                return Strings::isEmptyOrValueNull($this->currentQuestion->answer);
            case 'has_question_answer':
                $this->checkValue($this->currentQuestion, "if key=\"$key\"",  'can\'t be accessed outside of variation loop');
                return Strings::isNotEmptyOrValueNull($this->currentQuestion->answer);
        }

        throw new TemplateException("if key=\"$key\"", 'unknown key');
    }


    protected function retrievePrice(string $key): float
    {
        switch ($key) {
            case 'subtotal':
                return $this->receipt->order->price_subtotal;
            case 'transaction_costs':
                return $this->receipt->order->price_transaction;
            case 'discount_amount':
                return $this->receipt->order->price_discount;
            case 'tax_total':
                return $this->receipt->order->vat->order_vat;
            case 'total';
                return $this->receipt->order->price_total;

                // Product
            case 'product_price':
                $this->checkValue($this->currentProduct, "price value=\"$key\"",  'can\'t be accessed outside of product loop');
                return $this->currentProduct ?? 0;
            case 'product_subtotal':
                $this->checkValue($this->currentProduct, "price value=\"$key\"",  'can\'t be accessed outside of product loop');
                return $this->currentProduct->subtotal;
            case 'product_tax':
                $this->checkValue($this->currentProduct, "price value=\"$key\"",  'can\'t be accessed outside of product loop');
                return $this->currentProduct->getTax();

                // Taxes
            case 'product_tax':
                $this->checkValue($this->currentVatRow, "price value=\"$key\"",  'can\'t be accessed outside of tax loop');
                return $this->currentVatRow->vat_value;

                // Variation
            case 'variation_price':
                $this->checkValue($this->currentVatRow, "price value=\"$key\"",  'can\'t be accessed outside of variation loop');
                return $this->currentVariationValue->price;
        }

        throw new TemplateException("price value=\"$key\"", 'unknown key');
    }

    protected function retrieveValue(string $key): string
    {
        $value = $this->retrieveValueUnsafe($key);
        return ($value) ? $this->retrieveValueUnsafe($key) : "";
    }
    
    private function retrieveValueUnsafe(string $key): ?String
    {
        switch ($key) {
            case 'order_id':
                return $this->receipt->order->id;
            case 'order_date':
                return $this->receipt->order->created_at;
            case 'company_name':
                return $this->receipt->company->name;
            case 'order_number':
                return $this->receipt->order->confirmation_code;
            case 'order_ready_date':
                return $this->receipt->order->order_ready;
            case 'tablenumber':
                return $this->receipt->order->table_nr;
            case 'buzzernumber':
                return $this->receipt->order->buzzer_nr;
            case 'customer_name':
                return $this->receipt->order->name;
            case 'customer_phone':
                return $this->receipt->order->phone;
            case 'customer_email':
                return $this->receipt->order->email;
            case 'customer_adress':
                return $this->receipt->order->address;
            case 'customer_postal':
                return $this->receipt->order->postal;
            case 'customer_city':
                return $this->receipt->order->city;
            case 'order_notes':
                return $this->receipt->order->notes;
            case 'order_origin':
                return $this->receipt->order->origin;
            case 'payment_method':
                return $this->receipt->order->payment_method;
            case 'pin_receipt':
                return $this->receipt->order->hasPinTransactionReceipt()
                    ? $this->receipt->order->pin_transaction_receipt : '-- No pin receipt --';

                // Product
            case 'product_tax_tarif':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return number_format($this->currentProduct->vat_tarif, 0, ',', '.');
            case 'product_amount':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return $this->currentProduct->amount;
            case 'product_name':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return $this->currentProduct->name;
            case 'product_kitchen_info':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return $this->currentProduct->kitchen_info;
            case 'product_kitchen_info_or_name':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return (Strings::isNotEmptyOrValueNull($this->currentProduct->kitchen_info)) ?
                    $this->currentProduct->kitchen_info : $this->currentProduct->name;
            case 'product_notes':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return $this->currentProduct->notes;
            case 'product_category':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return $this->currentProduct->category->name;

                // Variation
            case 'variation_symbol':
                $this->checkValue($this->currentVariation, $key,  'can\'t be accessed outside of variation loop');
                return ($this->currentVariationValue->price > 0) ? ' + ' : ' - ';
            case 'variation_name':
                $this->checkValue($this->currentVariation, $key,  'can\'t be accessed outside of variation loop');
                return $this->currentVariationValue->name;
            case 'variation_kitchen_info':
                $this->checkValue($this->currentVariation, $key,  'can\'t be accessed outside of variation loop');
                return $this->currentVariationValue->kitchen_info;
            case 'variation_kitchen_info_or_name':
                $this->checkValue($this->currentProduct, $key,  'can\'t be accessed outside of product loop');
                return (Strings::isNotEmptyOrValueNull($this->currentVariationValue->kitchen_info)) ?
                    $this->currentVariationValue->kitchen_info : $this->currentVariationValue->name;

                // Taxes
            case 'tax_tarif':
                $this->checkValue($this->currentVatRow, $key,  'can\'t be accessed outside of tax loop');
                return number_format($this->currentVatRow->tarif, 0, ',', '.');

                // Questions
            case 'question':
                $this->checkValue($this->currentQuestion, $key,  'can\'t be accessed outside of questions loop');
                return $this->currentQuestion->question;
            case 'answer':
                $this->checkValue($this->currentQuestion, $key,  'can\'t be accessed outside of questions loop');
                return $this->currentQuestion->answer;
        }

        throw new TemplateException($key, 'is an unknown element');
    }

    protected function checkValue($value, string $command, string $message)
    {
        if (empty($value)) {
            throw new TemplateException($command, $message);
        }
    }
}
