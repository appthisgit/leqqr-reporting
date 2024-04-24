<?php

namespace App\Models;

use App\Helpers\ReceiptSettings;
use App\Http\Data\ProductData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    
    public ReceiptSettings $settings;
    private $products = null;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->settings = new ReceiptSettings();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'endpoint_id',
        'order',
        'result_message',
        'result_response',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

       /**
     * Retrieve all the required Products from the Order
     */
    public function getRequiredProducts(): array
    {
        return $this->order->data->products
            ->toCollection()
            ->filter(fn (ProductData $product) => !($product->category))
            ->all();
    }

    /**
     * Retrieve all Products from the Order after filtering on printable and zone and order them according to settings
     */
    public function getProducts(): array
    {
        if ($this->products == null) {
            $productsCollection = $this->order->data->products->toCollection()->filter(function (ProductData $product) {
                if ($product->category) {
                    if ($this->endpoint->filter_printable || $this->endpoint->filter_zone) {
                        return $product->inFilters(
                            $this->endpoint->filter_printable,
                            $this->endpoint->filter_zone
                        );
                    }

                    return true;
                }

                return false;
            });

            if (!empty($this->settings->sort)) {

                $callback = null;

                switch ($this->settings->sort) {
                    case 'category-order':
                        $callback = fn (ProductData $a, ProductData $b) => $a->category?->ordernum <=> $b->category?->ordernum;
                        break;
                    case 'category-order-reverse':
                        $callback = fn (ProductData $a, ProductData $b) => $b->category?->ordernum <=> $a->category?->ordernum;
                        break;
                    case 'category-name':
                        $callback = fn (ProductData $a, ProductData $b) => $a->category?->name <=> $b->category?->name;
                        break;
                    case 'category-name-reverse':
                        $callback = fn (ProductData $a, ProductData $b) => $b->category?->name <=> $a->category?->name;
                        break;
                    case 'product-name':
                        $callback = fn (ProductData $a, ProductData $b) => $a->name <=> $b->name;
                        break;
                    case 'product-name-reverse':
                        $callback = fn (ProductData $a, ProductData $b) => $b->name <=> $a->name;
                        break;
                }

                if ($callback) {
                    $productsCollection->sort($callback);
                } elseif ($this->settings->sort == 'reverse') {
                    $productsCollection->reverse();
                }
            }

            $this->products = $productsCollection->all();
        }

        return $this->products;
    }

    /**
     * Check if there remain any Products in the Order after filtering them on Printable and Zone
     */
    public function hasProducts(): bool
    {
        return count($this->getProducts());
    }
}
