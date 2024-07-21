<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'total' => $this->total,
            'meal_id' => $this->meal_id,
            'meal' => $this->meal,
            'note' => $this->note,
            'quantity' => $this->quantity
        ];
    }
}
