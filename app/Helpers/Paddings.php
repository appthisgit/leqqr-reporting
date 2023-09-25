<?php

namespace App\Helpers;

class Paddings extends Margins
{
    public function copy(): Paddings
    {
        return new Paddings(
            $this->top,
            $this->right,
            $this->bottom,
            $this->left
        );
    }
}
