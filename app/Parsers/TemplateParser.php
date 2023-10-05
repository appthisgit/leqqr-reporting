<?php

namespace App\Parsers;

use App\Exceptions\TemplateException;
use App\Helpers\ReceiptMods;
use App\Helpers\TextMods;
use App\Http\Data\ProductData;
use App\Models\Template;
use App\Parsers\Template\Lines\Line;
use App\Parsers\Template\Lines\ReceiptRow;
use App\Parsers\Template\Lines\TextLine;
use App\Parsers\Template\Printable;
use DOMDocument;
use DOMElement;
use DOMNode;

class TemplateParser extends FieldParser
{
    // Result
    private Printable $printable;

    // Receipt parsing
    private DOMElement $documentRoot;
    private Line $currentLine;

    public function load(Template $template)
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


    public function parse(): Printable
    {
        $this->printable = new Printable();
        $this->parseChildren($this->documentRoot);
        return $this->printable;
    }

    public function parseProduct(ProductData $currentProduct): Printable
    {

        $this->printable = new Printable();
        $this->currentProduct = $currentProduct;

        // TODO: is this correct?
        $this->parseChildren($this->documentRoot);

        return $this->printable;
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
                $this->currentLine = ReceiptRow::fromTextLine($textLine, $this->receipt->settings);
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

                if ($this->doIf($ifKey, $ifValue)) {
                    $this->parseChildren($node);
                }
                break;
            case 'product':
                $this->parseChildren($node);
                break;
            case 'foreach':
                if (empty($node->attributes) || empty($node->attributes->getNamedItem('items'))) {
                    throw new TemplateException('foreach', 'doesn\'t contain the attribute "items" with a key for the statement');
                }

                switch ($node->attributes->getNamedItem('items')->nodeValue) {
                    case 'products':
                        foreach ($this->receipt->getProductsFiltered() as $product) {
                            $this->currentProduct = $product;
                            $this->parseChildren($node);
                        }
                        break;
                    case 'taxes':
                        foreach ($this->receipt->order->vat->rows_vat as $vat) {
                            $this->currentVatRow = $vat;
                            $this->parseChildren($node);
                        }
                        break;
                    case 'variations':
                        foreach ($this->currentProduct->variations as $variation) {
                            $this->currentVariation = $variation;

                            foreach ($variation->values as $value) {
                                $this->currentVariationValue = $value;
                                $this->parseChildren($node);
                            }
                        }
                        break;
                    default:
                        throw new TemplateException('foreach items="' + $node->attributes->getNamedItem('items')->nodeValue + '"', 'unknown items value');
                }
                break;

                // non-functional
            case 'stripe':
                $this->currentLine = $this->createTextLine($node, ReceiptMods::divider($this->receipt->settings->stripeChar, $this->receipt->settings->widthCharAmount));
                $this->printable->lines[] = $this->currentLine;
                break;
            case 'item':
                if (!($this->currentLine instanceof ReceiptRow)) {
                    throw new TemplateException('item', 'found at unparsable location');
                }
                $this->parseChildren($node);
                break;
            case 'price':
                if (!($this->currentLine instanceof ReceiptRow)) {
                    throw new TemplateException('price', 'found at unparsable location');
                }
                if (!$node->hasAttributes() || empty($node->attributes->getNamedItem('value'))) {
                    throw new TemplateException('price', 'doesn\'t contain the attribute "value" with a key for a price value');
                }

                $key = $node->attributes->getNamedItem('value')->nodeValue;

                /** @var ReceiptRow */
                $currentRow = $this->currentLine;
                $currentRow->price = $this->retrievePrice($key);
                break;
            case 'text':
                if (!($this->currentLine instanceof TextLine)) {
                    throw new TemplateException('text', 'trying to add text to a non textual line');
                }

                $this->currentLine->appendText($node->textContent);
                break;
            default: // value nodes
                if ($node->hasChildNodes()) {
                    throw new TemplateException($node->nodeName, 'is unknown to have children');
                }
                $value = $this->retrieveValue($node->nodeName);

                if ($node->hasAttributes()) {
                    if (!empty($node->attributes->getNamedItem('wordwrap'))) {
                        $value = TextMods::wordwrap($value, $this->receipt->settings->widthCharAmount);
                    }
                    if (boolval($node->attributes->getNamedItem('center'))) {
                        $this->currentLine->center();
                    }
                }

                if (!($this->currentLine instanceof TextLine)) {
                    throw new TemplateException('text', 'trying to add text to a non textual line');
                }

                $this->currentLine->appendText($value);
                break;
        }
    }


    private function createTextLine(DOMNode $node, string $value): TextLine
    {
        $line = new TextLine($value, $this->receipt->settings);

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

}
