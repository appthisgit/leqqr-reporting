<?php

namespace App\Helpers;

class ReceiptSettings
{

    /**
     * Margins (actually paddings) around the whole print
     */
    public Margins $printMargins;

    /**
     * Margins (in most cases actually paddings) per line
     */
    public Margins $lineMargins;

    /**
     * Default Font
     */
    public string $font = 'Lucida Console';

    /**
     * Default font size
     */
    public int $fontSize = 9;

    /**
     * The size of the paper
     */
    public string $paperSize = '80mm';

    /**
     * The max amount of characters in a line
     */
    public ?int $widthCharAmount = 46;

    /**
     * The amount of characters in a price column
     */
    public int $priceCharAmount = 6;

    /**
     * The repeated text character for creating a stripe
     */
    public string $stripeChar = '-';

    /**
     * Should print the copyright at the footer
     */
    public bool $copyrightFooter = true;

    /**
     * Define if this receipt only displays a single product
     */
    public bool $singleProductTemplate = false;

    /**
     * Define the default way to sort products
     */
    public string $sort = '';

    public function __construct()
    {
        $this->lineMargins =  new Margins(2,);
        $this->printMargins = new Margins();
    }

    public function __toString()
    {
        return json_encode($this);
    }    
}
