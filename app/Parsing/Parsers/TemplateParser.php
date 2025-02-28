<?php

namespace App\Parsing\Parsers;

use App\Exceptions\TemplateException;
use App\Helpers\Alignment;
use App\Helpers\ReceiptSettings;
use App\Http\Data\ProductData;
use App\Models\Template;
use App\Parsing\Parsers\Template\FieldParser;
use App\Parsing\Parsers\Template\Lines\DividerLine;
use App\Parsing\Parsers\Template\Lines\ImageLine;
use App\Parsing\Parsers\Template\Lines\Line;
use App\Parsing\Parsers\Template\Lines\QRLine;
use App\Parsing\Parsers\Template\Lines\TableCell;
use App\Parsing\Parsers\Template\Lines\TableLine;
use App\Parsing\Parsers\Template\Lines\TextLine;
use App\Parsing\Parsers\Template\Printable;
use DOMDocument;
use DOMElement;
use DOMNode;

abstract class TemplateParser extends FieldParser
{
    // Result
    private Printable $printable;

    // Receipt parsing
    private DOMElement $documentRoot;
    private Line $currentLine;
    private ?array $images;
    private array $translations;

    public abstract function run();

    public function load(Template $template)
    {
        $this->images = $template->images;
        $this->translations = [];

        foreach ($template->translations as $translation) {
            $this->translations[$translation['key']] = [
                'de' => $translation['de'],
                'fr' => $translation['fr'],
                'en' => $translation['en'],
            ];
        }

        $doc = new DOMDocument();
        $doc->loadXML($template->content);

        $this->documentRoot = $doc->documentElement;

        foreach ($this->documentRoot->attributes as $attribute) {
            $this->setSetting($attribute->nodeName, $attribute->nodeValue);
        }
    }

    protected function setSetting(string $property, string $value)
    {
        switch ($property) {
            case 'products-sort':
                $this->receipt->settings->sort = $value;
                break;
            case 'copyright-footer':
                $this->receipt->settings->copyrightFooter = $value;
                break;
            case 'single-product-template':
                $this->receipt->settings->singleProductTemplate = $value;
                break;
            case 'font-size':
                $this->receipt->settings->fontSize = $value;
                break;
            case 'receipt-width':
            case 'receipt-width-char-amount':
                $this->receipt->settings->widthCharAmount = $value;
                break;
            case 'paper-size':
                $this->receipt->settings->paperSize = $value;
                break;
            case 'stripe-char':
                $this->receipt->settings->stripeChar = $value;
                break;
            case 'default-line-margin-top':
                $this->receipt->settings->lineMargins->top = $value;
                break;
            case 'default-line-margin-right':
                $this->receipt->settings->lineMargins->right = $value;
                break;
            case 'default-line-margin-bottom':
                $this->receipt->settings->lineMargins->bottom = $value;
                break;
            case 'default-line-margin-left':
                $this->receipt->settings->lineMargins->left = $value;
                break;
            case 'margin-top':
            case 'padding-top':
                $this->receipt->settings->printMargins->top = $value;
                break;
            case 'margin-right':
            case 'padding-right':
                $this->receipt->settings->printMargins->right = $value;
                break;
            case 'margin-bottom':
            case 'padding-bottom':
                $this->receipt->settings->printMargins->bottom = $value;
                break;
            case 'margin-left':
            case 'padding-left':
            case 'inset':
                $this->receipt->settings->printMargins->left = $value;
                break;
        }
    }


    protected function parse(): Printable
    {
        $this->printable = new Printable();
        $this->parseChildren($this->documentRoot);
        return $this->printable;
    }

    protected function parseProduct(ProductData $currentProduct): Printable
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
        $this->lineNumber = $node->getLineNo();

        switch ($node->nodeName) {
            case 'line':
                $this->setCurrentLine(new TextLine($this->receipt->settings), $node);
                $this->parseChildren($node);
                break;
            case 'stripe':
                $this->setCurrentLine(new DividerLine($this->receipt->settings), $node);
                break;
            case 'row':
                $this->setCurrentLine(new TableLine($this->receipt->settings), $node);
                $this->parseChildren($node);
                break;
            case 'qr':
            case 'qrcode':
                $this->setCurrentLine(new QRLine($this->receipt->settings), $node);
                $this->parseChildren($node);
                break;
            case 'image':
            case 'img':
                if (empty($this->images)) {
                    throw new TemplateException('image', 'No images uploaded', $this->lineNumber);
                }
                $img = array_shift($this->images);
                $this->setCurrentLine(new ImageLine($this->receipt->settings, $img), $node);
                break;
            case 'if':
                if (empty($node->attributes->getNamedItem('key'))) {
                    throw new TemplateException('if', 'doesn\'t contain the attribute "key" with a key for the statement', $this->lineNumber);
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
                    throw new TemplateException('foreach', 'doesn\'t contain the attribute "items" with a key for the statement', $this->lineNumber);
                }

                switch ($node->attributes->getNamedItem('items')->nodeValue) {
                    case 'products':
                        foreach ($this->receipt->getProducts() as $product) {
                            $this->lastCategory = $this->currentProduct->category ?? null;
                            $this->currentProduct = $product;
                            $this->parseChildren($node);
                        }
                        break;
                    case 'required_products':
                        foreach ($this->receipt->getRequiredProducts() as $product) {
                            $this->currentRequiredProduct = $product;
                            $this->parseChildren($node);
                        }
                        break;
                    case 'taxes':
                        foreach ($this->receipt->order->data->vat->rows_vat as $vat) {
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
                        foreach ($this->receipt->order->data->questions as $questionData) {
                            $this->currentQuestion = $questionData;
                            $this->parseChildren($node);
                        }
                        break;
                    default:
                        throw new TemplateException('foreach items="' . $node->attributes->getNamedItem('items')->nodeValue . '"', 'unknown items value', $this->lineNumber);
                }
                break;
            case 'cell':
                if (!($this->currentLine instanceof TableLine)) {
                    throw new TemplateException('column', 'found at unparsable location', $this->lineNumber);
                }

                /** @var TableLine */
                $tableLine = $this->currentLine;
                $tableLine->addCell(new TableCell($this->receipt->settings));
                $this->setAttributes($tableLine->currentCell, $node);

                $this->parseChildren($node);
                break;
            case 'item':
                if (!($this->currentLine instanceof TableLine)) {
                    throw new TemplateException('item', 'found at unparsable location', $this->lineNumber);
                }
                $this->parseChildren($node);
                break;
            case 'price':
                if (!($this->currentLine instanceof TableLine)) {
                    throw new TemplateException('price', 'found at unparsable location', $this->lineNumber);
                }
                if (!$node->hasAttributes() || empty($node->attributes->getNamedItem('value'))) {
                    throw new TemplateException('price', 'doesn\'t contain the attribute "value" with a key for a price value', $this->lineNumber);
                }

                $key = $node->attributes->getNamedItem('value')->nodeValue;

                /** @var TableLine */
                $tableLine = $this->currentLine;
                $tableLine->appendPrice($this->retrievePrice($key));
                break;
            case 'text':
                if (!($this->currentLine instanceof TextLine || $this->currentLine instanceof QRLine || $this->currentLine instanceof TableLine)) {
                    throw new TemplateException('text', 'trying to add text to a non textual line', $this->lineNumber);
                }

                $text = $node->textContent;
                $locale = $this->receipt->order->data->getLocale();
                if ($node->attributes->getNamedItem('translate') == true && $locale != 'nl') {
                    if (empty($this->translations[$node->textContent])) {
                        throw new TemplateException('text', "no translations added for '$text'", $this->lineNumber);
                    }
                    if (empty($this->translations[$node->textContent][$locale])) {
                        throw new TemplateException('text', "empty $locale translation for '$text'", $this->lineNumber);
                    }
                    $text = $this->translations[$node->textContent][$locale];
                }

                $this->currentLine->appendText($text);
                break;
            default: // value nodes
                if ($node->hasChildNodes()) {
                    throw new TemplateException($node->nodeName, 'is unknown to have children', $this->lineNumber);
                }
                if (!($this->currentLine instanceof TextLine  || $this->currentLine instanceof QRLine || $this->currentLine instanceof TableLine)) {
                    throw new TemplateException('text', 'trying to add text to a non textual line', $this->lineNumber);
                }
                if ($node->hasAttributes()) {
                    if ($node->attributes->getNamedItem('format')) {
                        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->retrieveValue($node->nodeName));
                        if ($date) {
                            $format = $node->attributes->getNamedItem('format')->nodeValue;
                            $this->currentLine->appendText($date->format($format));
                        } else {
                            throw new TemplateException($node->nodeName, 'has a format attribute which resulted in null', $this->lineNumber);
                        }
                    } else {
                        throw new TemplateException($node->nodeName, 'is unknown to have attributes other than "format" for a date, move current attributes to <line>', $this->lineNumber);
                    }
                } else {
                    $this->currentLine->appendText($this->retrieveValue($node->nodeName));
                }

                break;
        }
    }

    private function setCurrentLine(Line $line, DOMNode $node)
    {
        $this->currentLine = $line;
        $this->printable->lines[] = $line;
        $this->setAttributes($line, $node);
    }

    //TODO: This is very long, could this not be optimized more?
    private function setAttributes($object, DOMNODE $node)
    {
        $formattedTextObject = false;
        $usedTraits = class_uses($object);
        if ($usedTraits) {
            $formattedTextObject = (in_array('App\\Parsing\\Parsers\\Template\\Lines\\FormattedText', $usedTraits)) ? $object : false;
        }

        /** @var Line */
        $line = ($object instanceof Line) ? $object : false;

        /** @var TextLine */
        $textLine = ($object instanceof TextLine) ? $object : false;

        /** @var QRLine */
        $qrLine = ($object instanceof QRLine) ? $object : false;

        /** @var TableLine */
        $tableLine = ($object instanceof TableLine) ? $object : false;
        if (!$formattedTextObject) {
            $formattedTextObject = $tableLine;
        }

        /** @var TableCell */
        $tableCell = ($object instanceof TableCell) ? $object : false;


        foreach ($node->attributes as $attribute) {
            $v = $attribute->nodeValue;

            if ($formattedTextObject) {
                switch ($attribute->nodeName) {
                    case 'bold':
                    case 'bolded':
                        $formattedTextObject->bolded = $v;
                        break;
                    case 'underline':
                    case 'underlined':
                        $formattedTextObject->underlined = $v;
                        break;
                    case 'invert':
                    case 'inverted':
                        $formattedTextObject->inverted = $v;
                        break;
                    case 'align':
                        $formattedTextObject->alignment = match ($v) {
                            'center' => Alignment::center,
                            'centered' => Alignment::center,
                            'right' => Alignment::right,
                            default => Alignment::left
                        };
                        break;
                    case 'center':
                    case 'centered':
                        $formattedTextObject->alignment = ($v) ? Alignment::center : Alignment::left;
                        break;
                }
            }
            if ($line) {
                switch ($attribute->nodeName) {
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
            if ($textLine) {
                switch ($attribute->nodeName) {
                    case 'font-size':
                        $textLine->fontSize = $v;
                        break;
                    case 'font':
                        $textLine->font = $v;
                        break;
                }
            }
            if ($qrLine) {
                switch ($attribute->nodeName) {
                    case 'align':
                        $qrLine->alignment = match ($v) {
                            'center' => Alignment::center,
                            'centered' => Alignment::center,
                            'right' => Alignment::right,
                            default => Alignment::left
                        };
                        break;
                    case 'center':
                    case 'centered':
                        $qrLine->alignment = ($v) ? Alignment::center : Alignment::left;
                        break;
                    case 'size':
                        $qrLine->size = $v;
                        break;
                }
            }
            if ($tableLine) {
                switch ($attribute->nodeName) {
                    case 'width':
                        $tableLine->width = $v;
                        break;
                }
            }
            if ($tableCell) {
                switch ($attribute->nodeName) {
                    case 'span':
                        $tableCell->span = $v;
                        break;
                }
            }
        }
    }
}
