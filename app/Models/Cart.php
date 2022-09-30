<?php

namespace App\Models;
use App\Support\Price;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'identifier',
        'user_id',
        'payment_method',
      
    ];
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
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
            'identifier' => $this->identifier,
            'payment_method' => (int) $this->payment_method,
            'notes' => $this->notes,
            'sub_total' => new Price($this->sub_total),
            'shipping_cost' => new Price($this->shipping_cost),
            'discount' => new Price($this->discount),
            'total' => new Price($this->sub_total),
            'items' => $this->items,
        ];
    }
}
