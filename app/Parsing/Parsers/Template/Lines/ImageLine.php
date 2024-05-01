<?php

namespace App\Parsing\Parsers\Template\Lines;

use App\Helpers\ReceiptSettings;
use Illuminate\Support\Facades\Storage;

class ImageLine extends Line
{

    public function __construct(
        ReceiptSettings $defaults,
        private string $image,
    ) {
        parent::__construct($defaults);
    }

    public function getImage(): string 
    {
        return Storage::path('public/' . $this->image);
    }
}
