<?php

namespace App\Parsers\Html;

trait HtmlElement
{

    protected $styles;
    protected $classes;

    protected function copyAttributes($line)
    {
        foreach ($line as $attr => $value) {
            $this->{$attr} = $value;
        }
    }

    protected function setNonDefaultSpacingStyle(string $property, string $type = 'margin')
    {
        if ($this->margins->{$property} != $this->defaults->lineMargins->{$property}) {
            $this->styles[] = $type . '-' . $property . ':' . $this->margins->{$property} . 'px';
        }
    }

    protected function setNonDefaultStyle(string $property, string $style, string $unit = '')
    {
        if ($this->{$property} != $this->defaults->{$property}) {
            $this->styles[] = $style . ':' . $this->{$property} . $unit;
        }
    }

    protected function setNonDefaultClass(string $property)
    {
        if ($this->{$property}) {
            $this->classes[] = $property;
        }
    }

    protected function prepareStyling()
    {
        $this->styles = array();
        $this->classes = array();

        $this->setNonDefaultSpacingStyle('top');
        $this->setNonDefaultSpacingStyle('right', 'padding');
        $this->setNonDefaultSpacingStyle('bottom');
        $this->setNonDefaultSpacingStyle('left', 'padding');
    }

    protected function implodeStyling(): string
    {
        $formatting = '';
        if ($this->classes) {
            $formatting .= sprintf(' class="%s"', implode(' ', $this->classes));
        }
        if ($this->styles) {
            $formatting .= sprintf(' style="%s"', implode('; ', $this->styles));
        }
        return $formatting;
    }

    public abstract function getHtml(): string;

    public function __toString()
    {
        return $this->getHtml();
    }
}
