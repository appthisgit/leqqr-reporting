<?php

namespace App\Parsers\Template\Lines;

abstract class Element extends Line
{

    protected $styles = array();
    protected $classes = array();

    public function __construct(
        Line $Line
    ) {
        parent::__construct($Line->defaults);

        foreach ($Line as $attr => $value) {
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

    protected abstract function formTag(string $formatting);

    public function getHtml(): string
    {
        $this->setNonDefaultSpacingStyle('top');
        $this->setNonDefaultSpacingStyle('right', 'padding');
        $this->setNonDefaultSpacingStyle('bottom');
        $this->setNonDefaultSpacingStyle('left', 'padding');

        $this->setNonDefaultClass('centered');

        $formatting = '';
        if ($this->classes) {
            $formatting .= sprintf(' class="%s"', implode(' ', $this->classes));
        }
        if ($this->styles) {
            $formatting .= sprintf(' style="%s"', implode('; ', $this->styles));
        }

        $tag = $this->formTag($formatting);

        $this->styles = array();
        $this->classes = array();

        return $tag;
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
