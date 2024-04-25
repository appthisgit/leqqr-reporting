<?php

namespace App\Parsing\Parsers\Html;

trait HtmlElement
{
    protected $attributes;

    protected function copyAttributes($line)
    {
        foreach ($line as $attr => $value) {
            $this->{$attr} = $value;
        }
    }

    private function addNonDefaultSpacingStyle(string $property, string $type = 'margin')
    {
        if ($this->margins->{$property} != $this->defaults->lineMargins->{$property}) {
            $this->attributes['style'][] = $type . '-' . $property . ':' . $this->margins->{$property} . 'px';
        }
    }

    protected function addNonDefaultStyle(string $property, string $style, string $unit = '')
    {
        if ($this->{$property} != $this->defaults->{$property}) {
            $this->attributes['style'][] = $style . ':' . $this->{$property} . $unit;
        }
    }

    protected function addNonDefaultClass(string $property)
    {
        if ($this->{$property}) {
            $this->attributes['class'][] = $property;
        }
    }

    protected function addAttribute(string $attribute, string $value)
    {
        $this->attributes[$attribute] = $value;
    }

    protected function prepareAttributes()
    {
        $this->attributes = array();

        $this->addNonDefaultSpacingStyle('top');
        $this->addNonDefaultSpacingStyle('right', 'padding');
        $this->addNonDefaultSpacingStyle('bottom');
        $this->addNonDefaultSpacingStyle('left', 'padding');
    }

    protected function formatAttributes(): string
    {
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
