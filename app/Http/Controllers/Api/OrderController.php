<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Support\Cart\CartServices;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Repositories\OrderRepository;
use App\Http\Resources\OrderResource;

use function PHPUnit\Framework\returnSelf;

class OrderController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * The order repository instance.
     *
     * @var \App\Repositories\OrderRepository
     */
    private $repository;

    /**
     * Create Order Controller instance.
     *
     * @param \App\Repositories\OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->middleware('auth:sanctum');

        $this->repository = $repository;
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cartServices = app(CartServices::class);

        $cart = $cartServices
            ->setUser($request->user())
            ->setIdentifier($request->header('cart-identifier'))
            ->getCart();

           

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => [__('The cart is empty')],
            ]);
        }
     

        $order = $this
            ->repository
            ->setUser($request->user())
            ->create($cart);
           
        
        return new OrderResource($order);
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
         return new OrderResource($order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
