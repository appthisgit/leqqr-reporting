<?php

namespace App\Helpers;

class Margins
{

    public function __construct(
        public int $top = 0,
        public int $right = 0,
        public int $bottom = 0,
        public int $left = 0,
    ) {
    }

    public function setAll(int $amount)
    {
        $this->top = $this->right = $this->bottom = $this->left = $amount;
    }

    public function set(int $top, int $right, int $bottom, int $left)
    {
        $this->top = $top;
        $this->right = $right;
        $this->bottom = $bottom;
        $this->left = $left;
    }

    public function copy(): Margins
    {
        return new Margins(
            $this->top,
            $this->right,
            $this->bottom,
            $this->left
        );
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
