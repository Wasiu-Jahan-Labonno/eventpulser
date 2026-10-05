<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReserveTicketRequest;
use App\Models\Event;
use App\Models\TicketTier;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    public function store(ReserveTicketRequest $request, Event $event, TicketTier $tier): JsonResponse
    {
        $reservation = $this->reservationService->reserve(
            tier: $tier,
            quantity: $request->validated('quantity'),
            email: $request->validated('email'),
            userId: $request->user()?->id
        );

        return response()->json([
            'message' => 'Tickets held successfully for 10 minutes.',
            'reservation_id' => $reservation->id,
            'expires_at' => $reservation->expires_at->toIso8601String(),
        ], 201);
    }
}
