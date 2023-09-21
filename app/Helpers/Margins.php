<?php

namespace App\Helpers;

class Margins
{

    public function __construct(
        public int $top,
        public int $right,
        public int $bottom,
        public int $left,
    ) {
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
}
