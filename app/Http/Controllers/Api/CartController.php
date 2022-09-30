<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Support\Cart\CartServices;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function createcart()
    {
        $cartServices = app(CartServices::class);
        return $cartServices
            ->setUser(auth('sanctum')->user())
            ->paymentMethod(request()->payment_method)
            ->shippingCost(request()->shipping_cost)
            ->getCart();
    }

    public function getcart(Request $Request)
    {
        if($Request->header('cart-identifier') == null && auth('sanctum')->user() == null) {
            return response()->json([
                'message' => trans('auth.messages.forget-password-code-sent'),
             
            ]);
        }
        
        else

        $cartServices = app(CartServices::class);
        return $cartServices
            ->setUser(auth('sanctum')->user())
            ->setIdentifier(request()->header('cart-identifier'))
            ->getCart();
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);
      
        $cartServices = app(CartServices::class);

        $cartServices
            ->setUser(auth()->user())
            ->setIdentifier($request->header('cart-identifier'));

        $cartServices->addItem(
            $request->product_id,
            $request->quantity,
            

        );

        return $cartServices->getCart();
    }

    public function updateItem(CartItem $cartItem, Request $request)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

       
        if ($cartItem->product->quantity < $request->quantity ) {
            throw ValidationException::withMessages([
                'coupon' => [__('The coupon you entered is invalid.')],
            ]);
        }

        $cartItem->update($request->only('quantity'));

        $cartServices = app(CartServices::class);

        $cart = $cartServices
            ->setUser(auth()->user())
            ->setIdentifier($request->header('cart-identifier'))
            ->getCart();

        $cartServices->updateTotals();

       // broadcast(new ItemUpdated($cartItem))->toOthers();

        return $cart->refresh();
    }


    public function deleteItem(CartItem $cartItem, Request $request)
    {
        $cartItem->delete();

        $cartServices = app(CartServices::class);

        $cart = $cartServices
            ->setUser(auth()->user())
            ->setIdentifier($request->header('cart-identifier'))
            ->getCart();

        $cartServices->updateTotals();

       // broadcast(new ItemDeleted($cart->refresh()))->toOthers();

        return $cart->refresh();
    }



}
