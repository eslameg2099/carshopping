<?php

namespace App\Http\Resources;
use App\Support\Price;

use App\Support\Date;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status_order'=>$this->status,
            'sub_total' => new price($this->sub_total),
            'discount' => new price($this->discount),
            'shipping_cost' => new price($this->shipping_cost),
            'total' => new price($this->total),
            'create_at' => new Date($this->created_at),
            'payment_method' => $this->payment_method,
            'items' =>  $this->items()->get(),

        ];
    }
}
