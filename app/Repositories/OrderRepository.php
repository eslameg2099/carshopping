<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Laraeast\LaravelSettings\Facades\Settings;

class OrderRepository
{
    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    protected $order;

    /**
     * The user instance.
     *
     * @var \App\Models\Customer
     */
    protected $user;

    /**
     * Get the order instance.
     *
     * @return \App\Models\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the order instance.
     *
     * @param \App\Models\Order $order
     * @return \App\Repositories\OrderRepository
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the user instance.
     *
     * @return \App\Models\Customer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the user instance.
     *
     * @param \App\Models\Customer $user
     * @return \App\Repositories\OrderRepository
     */
    public function setUser(Customer $user)
    {
        $this->user = $user;

        return $this;
    }

   

    /**
     * Store the newly created order in the storage.
     *
     * @param Cart $cart
     * @return \App\Models\Order
     */
    public function create(Cart $cart)
    {
        DB::beginTransaction();
        $data = $this->qualifyData($cart);

        $order = $this->getUser()->orders()->create($data);
        $items = $cart->items;
        foreach ($items as $item){
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'updated' => $item->updated,
            ]);
            $product = $item->product;
            $product->update(['quantity' => $product->quantity - $item->quantity]);


        }

        // TODO: save cart payment to order.

        $cart->delete();

        $this->setOrder($order);
        DB::commit();

        return $this->getOrder();
    }

    /**
     * Qualify data for the order.
     *
     * @param Cart $cart
     * @return array
     */
    public function qualifyData(Cart $cart)
    {
        $address = $cart->address;

        $order = [
            'address_id' => $cart->address_id,
            'payment_method' => $cart->payment_method,
            'shipping_cost' => $cart->shipping_cost_shop,
            'discount' => $cart->discount,
            'discount_percentage' => $cart->discount_percentage,
            'notes' => $cart->notes,
            'System_tax' => Settings::get('added'),
            'sub_total' => $cart->sub_total,
            'total' => $cart->sub_total,
            
            'products' => $cart->items->map(function (CartItem $item) {
                return [
                    'id' => $item->product_id,
                    'product_id' => $item->product_id,
                    'price' => $item->product->getPrice(),
                    'quantity' => $item->quantity,
                 
                ];
            })->toArray(),
            'orders' => [],
        ];

       

        return $order;
    }

    /**
     * Get the shipping cost value.
     *
     * @param \App\Models\Shop $shop
     * @param \App\Models\Address $address
     * @return float
     */
    public function calculateShippingCost(CartItem $item)
    {
        if($item->shipping == '1')
        {
           return $item->z;
        }
        else 0;

    }
}
