<?php

namespace App\Parsing\Parsers\Html;

use App\Parsing\Parsers\Template\Lines\Line;

abstract class HtmlElement
{
    private $attributes;

    public function __construct(
        private ?Line $line
    ) {
        $this->attributes = array();
    }

    private function addNonDefaultSpacingStyle(Line $line, string $property, string $type = 'padding')
    {
        if ($line->margins->{$property} != $line->defaults->lineMargins->{$property}) {
            $this->attributes['style'][] = $type . '-' . $property . ':' . $line->margins->{$property} . 'px';
        }
    }

    protected function addNonDefaultStyle(Line $line, string $property, string $style, string $unit = '')
    {
        if ($line->{$property} != $line->defaults->{$property}) {
            $this->attributes['style'][] = $style . ':' . $line->{$property} . $unit;
        }
    }

    protected function addNonDefaultClass(Line $line, string $property)
    {
        if ($line->{$property}) {
            $this->attributes['class'][] = $property;
        }
    }

    protected function addAttribute(string $attribute, string $value)
    {
        $this->attributes[$attribute] = $value;
    }

    protected function formatAttributes(): string
    {
        if ($this->line) {
            $this->addNonDefaultSpacingStyle($this->line, 'top');
            $this->addNonDefaultSpacingStyle($this->line, 'right');
            $this->addNonDefaultSpacingStyle($this->line, 'bottom');
            $this->addNonDefaultSpacingStyle($this->line, 'left');
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
