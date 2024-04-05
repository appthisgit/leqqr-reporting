<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\PrintSettings;
use Illuminate\Support\Facades\Storage;

class ImageLine extends Line
{

    public function __construct(
        PrintSettings $defaults,
        string $image,
    ) {
        parent::__construct($defaults);
        $this->value = $image;
    }

    public function getImage(): string 
    {
        return Storage::path('public/' . $this->value);
    }

    public function __toString()
    {
        return $this->getImage();
    }
}
