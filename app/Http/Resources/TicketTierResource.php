<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketTierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => [
                'formatted' => '$' . number_format($this->price_cents / 100, 2),
                'amount_cents' => $this->price_cents,
            ],
            'available_tickets' => $this->remaining_capacity,
            'is_sold_out' => $this->remaining_capacity <= 0,
        ];
    }
}
