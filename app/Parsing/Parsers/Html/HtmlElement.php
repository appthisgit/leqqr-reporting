<?php

namespace App\Parsing\Parsers\Html;

use App\Helpers\Alignment;
use App\Parsing\Parsers\Template\Lines\Line;
use Log;

abstract class HtmlElement
{
    private $attributes;

    public function __construct(
        private ?Line $line
    ) {
        $this->attributes = array();
    }

    /**
     * Set the margins of this HtmlElement to the non default values of a Line object
     */
    protected function setMargins(Line $line)
    {
        foreach (['top', 'right', 'bottom', 'left'] as $direction) {
            if ($line->margins->{$direction} != $line->defaults->lineMargins->{$direction}) {
                $this->addAttribute('style', 'padding-' . $direction . ':' . $line->margins->{$direction} . 'px');
            }
        }
    }

    /**
     * Set the alignment of this HtmlElement
     */
    protected function setAlignment(Alignment $alignment) {
        if ($alignment != Alignment::left) {
            Log::debug($alignment->name);
            $this->addAttribute('class', 'align-' . $alignment->name);
        }
    }

    /**
     * Add a style for a property that isn't the same as default
     */
    protected function toggleStyle(Line $line, string $property, string $style, string $unit = '')
    {
        if ($line->{$property} != $line->defaults->{$property}) {
            $this->addAttribute('style', $style . ':' . $line->{$property} . $unit);
        }
    }

    /**
     * Add classes for properties which are true
     */
    protected function toggleClass(Line $line, string $property)
    {
        if ($line->{$property}) {
            $this->addAttribute('class', $property);
        }
    }

    /**
     * Add a attribute to this HtmlElement
     */
    protected function addAttribute(string $attribute, string $value)
    {
        $this->attributes[$attribute][] = $value;
    }

    /**
     * Format and return all attributes as an easy string
     */
    protected function formatAttributes(): string
    {
        if ($this->line) {
            $this->setMargins($this->line);
        }

        $formatting = '';
        foreach ($this->attributes as $key => $value) {
            if (is_array($value)) {
                $value = implode(($key == 'style') ? ';' : ' ', $value);
            }
            $formatting .= sprintf(' %s="%s"', $key, $value);
        }
        return $formatting;
    }

    public abstract function getHtml(): string;

    public function __toString()
    {
        return $this->getHtml();
    }
}
