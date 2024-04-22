<?php

namespace App\Parsers\Template\Lines;

use App\Helpers\PrintSettings;
use Illuminate\Support\Facades\Storage;

class ImageLine extends Line
{

    public function __construct(
        PrintSettings $defaults,
        private string $image,
    ) {
        parent::__construct($defaults);
    }

    public function getImage(): string 
    {
        return Storage::path('public/' . $this->image);
    }
}
