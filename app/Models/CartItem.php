<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Price;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
  

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => new Price($this->price),
            'total'=> new Price($this->price * $this->quantity),
            'updated' => $this->wasUpdated(),
            'updated_message' => $this->getUpdateMessage(),
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ],
        ];
    }

    public function wasUpdated()
    {
      

        return $this->quantity > $this->product->quantity
            || $this->price != $this->product->getPrice();
    }

    /**
     * @return bool
     */
    public function getUpdateMessage()
    {
       
        if ($this->quantity > $this->product->quantity) {
            return __('الكمية المطلوبة غير متوفرة حاليا');
        }
        if ($this->price != $this->product->getPrice()) {
            return __('تم تحديث سعر المنتج');
        }
    }
}
