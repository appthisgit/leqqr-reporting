<?php

namespace App\Parsers;

use App\Exceptions\TemplateException;
use App\Helpers\ProductSorting;
use App\Helpers\ReceiptMods;
use App\Http\Data\ProductData;
use App\Models\Template;
use App\Parsers\Template\Lines\ImageLine;
use App\Parsers\Template\Lines\Line;
use App\Parsers\Template\Lines\ReceiptRow;
use App\Parsers\Template\Lines\TextLine;
use App\Parsers\Template\Printable;
use DOMDocument;
use DOMElement;
use DOMNode;

class TemplateParser extends FieldParser
{

    protected bool $parsedProducts = false;

    // Result
    private Printable $printable;

    // Receipt parsing
    private DOMElement $documentRoot;
    private Line $currentLine;
    private ?array $images;


    public function load(Template $template)
    {
        $this->images = $template->images;

        $doc = new DOMDocument();
        $doc->loadXML($template->content);

        $this->documentRoot = $doc->documentElement;

        foreach ($this->documentRoot->attributes as $attribute) {
            switch ($attribute->nodeName) {
                case 'products-sort':
                    $this->receipt->settings->sort = $attribute->nodeValue;
                    break;
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
                $this->setCurrentLine(new TextLine($this->receipt->settings), $node);
                $this->parseChildren($node);
                break;
            case 'row':
                $this->setCurrentLine(new ReceiptRow($this->receipt->settings), $node);
                $this->parseChildren($node);
                break;
            case 'image':
            case 'img':
                if (empty($this->images)) {
                    throw new TemplateException('image', 'No images uploaded');
                }
                $img = array_shift($this->images);
                $this->setCurrentLine(new ImageLine($img, $this->receipt->settings), $node);
                break;
            case 'if':
                if (empty($node->attributes->getNamedItem('key'))) {
                    throw new TemplateException('if', 'doesn\'t contain the attribute "key" with a key for the statement');
                }
                $ifValueNode = $node->attributes->getNamedItem('value');
                $ifKey = $node->attributes->getNamedItem('key')->nodeValue;
                $ifValue = (empty($ifValueNode)) ? null : $ifValueNode->nodeValue;

                if ($this->doIf($ifKey, $ifValue)) {
                    $this->parseChildren($node);
                }
                break;
            case "product":
                $this->parseChildren($node);
                break;
            case 'foreach':
                if (empty($node->attributes->getNamedItem('items'))) {
                    throw new TemplateException('foreach', 'doesn\'t contain the attribute "items" with a key for the statement');
                }

                switch ($node->attributes->getNamedItem('items')->nodeValue) {
                    case 'products':
                        foreach ($this->receipt->getProductsFiltered() as $product) {
                            $this->currentProduct = $product;
                            $this->parseChildren($node);
                            $this->parsedProducts = true;
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

                            foreach ($variation->selected as $value) {
                                $this->currentVariationValue = $value;
                                $this->parseChildren($node);
                            }
                        }
                        break;
                    case 'questions':
                        foreach ($this->receipt->order->questions as $questionData) {
                            $this->currentQuestion = $questionData;
                            $this->parseChildren($node);
                        }
                        break;
                    default:
                        throw new TemplateException('foreach items="' . $node->attributes->getNamedItem('items')->nodeValue . '"', 'unknown items value');
                }
                break;
            case 'stripe':
                $stripe = new TextLine($this->receipt->settings);
                $stripe->appendText(ReceiptMods::divider($this->receipt->settings->stripeChar, $this->receipt->settings->widthCharAmount));
                $this->setCurrentLine($stripe, $node);
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
                if (!($this->currentLine instanceof TextLine)) {
                    throw new TemplateException('text', 'trying to add text to a non textual line');
                }
                if ($node->hasAttributes()) {
                    if ($node->attributes->getNamedItem('format')) {
                        $date = \DateTime::createFromFormat('Y-m-d H:i:s' ,$this->retrieveValue($node->nodeName));
                        if ($date) {
                            $format = $node->attributes->getNamedItem('format')->nodeValue;
                            $this->currentLine->appendText($date->format($format));
                        } else {
                            throw new TemplateException($node->nodeName, 'has a format attribute which resulted in null');
                        }
                    }
                    else {
                        throw new TemplateException($node->nodeName, 'is unknown to have attributes other than "format" for a date, move current attributes to <line>');
                    }
                }
                else {
                    $this->currentLine->appendText($this->retrieveValue($node->nodeName));
                }

                break;
        }
    }


    private function setCurrentLine(Line $line, DOMNode $node)
    {
        $this->currentLine = $line;
        $this->printable->lines[] = $line;

        foreach ($node->attributes as $attribute) {

            if ($line instanceof TextLine) {

                /** @var TextLine */
                $textLine = $line;
                $v = $attribute->nodeValue;

                switch ($attribute->nodeName) {
                    case 'wordwrap':
                        $textLine->setWordwrap();
                    case 'font-size':
                        $textLine->fontSize = $v;
                        break;
                    case 'font':
                        $textLine->font = $v;
                        break;
                    case 'bold':
                        $textLine->bolded = $v;
                        break;
                    case 'inverted':
                        $textLine->inverted = $v;
                        break;
                }
            }

            switch ($attribute->nodeName) {
                case 'center':
                    $line->centered = $v;
                    break;
                case 'margin-top':
                    $line->margins->top = $v;
                    break;
                case 'margin-right':
                    $line->margins->right = $v;
                    break;
                case 'margin-bottom':
                    $line->margins->bottom = $v;
                    break;
                case 'margin-left':
                    $line->margins->left = $v;
                    break;
            }
        }
    }
}
