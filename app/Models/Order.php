<?php

namespace App\Models;

use App\Http\Data\OrderData;
use App\Http\Data\ProductData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public $incrementing = false;
    private $products = null;


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => OrderData::class,
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'confirmation_code',
        'data',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Retrieve all the required Products from the Order
     */
    public function getRequiredProducts(): array
    {
        return $this->data->products
            ->toCollection()
            ->filter(fn (ProductData $product) => !($product->category))
            ->all();
    }

    /**
     * Retrieve all Products from the Order after filtering on printable and zone and order them according to settings
     */
    public function getProducts(string $sort = ''): array
    {
        if ($this->products == null) {
            $productsCollection = $this->data->products->toCollection()->filter(function (ProductData $product) {
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

            if (!empty($sort)) {

                $callback = null;

                switch ($sort) {
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
                } elseif ($sort == 'reverse') {
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
        $count = count($this->getProducts());
        $this->products = null;
        return $count;
    }
}
