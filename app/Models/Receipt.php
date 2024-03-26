<?php

namespace App\Models;

use App\Helpers\ReceiptSettings;
use App\Http\Data\OrderData;
use App\Http\Data\ProductData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;

class Receipt extends Model
{
    public ReceiptSettings $settings;

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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'order' => OrderData::class,
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'company_id',
        'endpoint_id',
        'order',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }

    public function getProductsFiltered(): array
    {
        $productsCollection = $this->order->products->toCollection()->filter(function (ProductData $product) {
            if ($product->category) {
                if ($this->filter_printable || $this->filter_zone) {
                    return $product->inFilters($this->filter_printable, $this->filter_zone);
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

        return $productsCollection->all();
    }
}
