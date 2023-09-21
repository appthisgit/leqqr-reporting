<?php

namespace App\Parsers\In;

use App\Exceptions\TemplateException;
use App\Helpers\Strings;
use App\Helpers\TextMods;
use App\Http\DTOs\Leqqr\Product;
use App\Http\DTOs\Leqqr\Receipt;
use App\Http\DTOs\Leqqr\Variation;
use App\Http\DTOs\Out\Base\Lines\Line;
use App\Http\DTOs\Out\Base\Lines\ReceiptRow;
use App\Http\DTOs\Out\Base\Lines\TextLine;
use App\Http\DTOs\Out\Base\Printable;
use App\Models\Template;
use DOMDocument;
use DOMElement;
use DOMNode;

class PrintableParser
{
    // Result
    private Printable $printable;

    // Receipt parsing
    private DOMElement $documentRoot;
    private Line $currentLine;
    private Product $currentProduct;
    private Variation $currentVariation;

    public function __construct(
        private readonly Receipt $receipt,
        private readonly bool $filterPrintable,
        private readonly string $filterZone,
    ) {
    }

    public function read(Template $template)
    {
        $doc = new DOMDocument();
        $doc->loadXML($template->content);

        $this->documentRoot = $doc->documentElement;

        foreach ($this->documentRoot->attributes as $attribute) {
            switch ($attribute->nodeName) {
                case 'copyright-footer':
                    $this->receipt->settings->copyrightFooter = $attribute->nodeValue;
                    break;
                case 'single-product-template':
                    $this->receipt->settings->singleProductTemplate = $attribute->nodeValue;
                    break;
                case 'font-size':
                    $this->receipt->settings->fontSize = $attribute->nodeValue;
                    break;
                case 'receipt-width':
                case 'receipt-width-char-amount':
                    $this->receipt->settings->widthCharAmount = $attribute->nodeValue;
                    break;
                case 'receipt-width-paper':
                    $this->receipt->settings->widthPaper = $attribute->nodeValue;
                    break;
                case 'stripe-char':
                    $this->receipt->settings->stripeChar = $attribute->nodeValue;
                    break;

                case 'default-line-margins':
                    // TODO: is this required?
                    // value = Integer.parseInt(item.getNodeValue());
                    // receipt.getSettings().lineMargins.top = value;
                    // receipt.getSettings().lineMargins.right = value;
                    // receipt.getSettings().lineMargins.bottom = value;
                    // receipt.getSettings().lineMargins.left = value;
                    break;
                case 'default-line-margin-top':
                    $this->receipt->settings->lineMargins->top = $attribute->nodeValue;
                    break;
                case 'default-line-margin-right':
                    $this->receipt->settings->lineMargins->right = $attribute->nodeValue;
                    break;
                case 'default-line-margin-bottom':
                    $this->receipt->settings->lineMargins->bottom = $attribute->nodeValue;
                    break;
                case 'default-line-margin-left':
                    $this->receipt->settings->lineMargins->left = $attribute->nodeValue;
                    break;

                case 'paddings':
                case 'margins':
                    // TODO: is this required?
                    // value = Integer.parseInt(item.getNodeValue());
                    // receipt.getSettings().paddings.top = value;
                    // receipt.getSettings().paddings.right = value;
                    // receipt.getSettings().paddings.bottom = value;
                    // receipt.getSettings().paddings.left = value;
                    break;
                case 'margin-top':
                case 'padding-top':
                    $this->receipt->settings->paddings->top = $attribute->nodeValue;
                    break;
                case 'margin-right':
                case 'padding-right':
                    $this->receipt->settings->paddings->right = $attribute->nodeValue;
                    break;
                case 'margin-bottom':
                case 'padding-bottom':
                    $this->receipt->settings->paddings->bottom = $attribute->nodeValue;
                    break;
                case 'margin-left':
                case 'padding-left':
                case 'inset':
                    $this->receipt->settings->paddings->left = $attribute->nodeValue;
                    break;
            }
        }
    }


    public function parse()
    {
        $this->printable = new Printable();
        $this->parseChildren($this->documentRoot);
    }

    private function parseChildren(DOMElement $node)
    {
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $node) {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $this->parseNode($node);
                }
            }
        }
    }

    private function parseNode(DOMNode $node)
    {
        switch ($node->nodeName) {
            case 'line':
                $this->currentLine = $this->createTextLine($node, '');
                $this->printable->lines[] = $this->currentLine;

                $this->parseChildren($node);
                break;
            case 'row':
                $textLine = $this->createTextLine($node, '');
                $this->currentLine = ReceiptRow::fromTextLine($textLine);
                $this->printable->lines[] = $this->currentLine;

                $this->parseChildren($node);
                break;
            case 'image':
            case 'img':
                //TODO: not yet supported
                break;
            case 'if':
                if (!$node->hasAttributes() || empty($node->attributes->getNamedItem('key'))) {
                    throw new TemplateException('if', 'doesn\'t contain the attribute "key" with a key for the statement');
                }
                $ifValueNode = $node->attributes->getNamedItem('value');
                $ifKey = $node->attributes->getNamedItem('key')->nodeValue;
                $ifValue = (empty($ifValueNode)) ? null : $ifValueNode->nodeValue;


                break;
            case 'product':
                break;
            case 'foreach':
                break;
            case 'stripe':
                break;
            case 'item':
                break;
            case 'price':
                break;
            case 'text':
            default:
                break;
        }
    }

    private function createTextLine(DOMNode $node, string $value): TextLine
    {
        $line = new TextLine($value);

        foreach ($node->attributes as $attribute) {
            switch ($attribute->nodeName) {
                case 'font-size':
                    $line->fontSize = $attribute->nodeValue;
                    break;
                case 'font':
                    $line->font = $attribute->nodeValue;
                    break;
                case 'bold':
                    $line->bolded = $attribute->nodeValue;
                    break;
                default:
                    $line = $this->setLineFields($attribute->nodeName, $attribute->nodeValue, $line);
                    break;
            }
        }

        return $line;
    }

    private function setLineFields(string $key, string $value, Line $line): Line
    {
        switch ($key) {
            case 'center':
                $line->centered = $value;
                break;
            case 'margin-top':
                $line->margins->top = $value;
                break;
            case 'margin-right':
                $line->margins->right = $value;
                break;
            case 'margin-bottom':
                $line->margins->bottom = $value;
                break;
            case 'margin-left':
                $line->margins->left = $value;
                break;
        }

        return $line;
    }

    private function doIf(string $key, ?string $value): bool
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
            case 'has_delivery_costs':
                return !empty($this->receipt->order->price_delivery);
            case 'has_transaction_costs':
                return !empty($this->receipt->order->price_transaction);
            case 'has_discounts':
                return !empty($this->receipt->order->price_discount);
            case 'has_taxes':
                return !empty($this->receipt->order->price_tax);
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
            case 'has_product_kitchen_info':
                $this->checkValue($this->currentProduct, "if key=\"$key\"",  'can\'t be accessed outside of product loop');

        }
    }

    private function checkValue($value, string $command, string $message)
    {
        if (empty($value)) {
            throw new TemplateException($command, $message);
        }
    }
}
