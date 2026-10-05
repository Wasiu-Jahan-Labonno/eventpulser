<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\TicketTier;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ReservationService
{
    public function reserve(TicketTier $tier, int $quantity, string $email, ?int $userId = null): Reservation
    {
        $lockKey = "lock:ticket_tier:{$tier->id}";
        // 1. Acquire Redis atomic distributed lock (block max 4 seconds, auto-release after 10s)
        return Cache::lock($lockKey, 10)->block(4, function () use ($tier, $quantity, $email, $userId) {
            // 2. Wrap operations inside a strict DB transaction
            return DB::transaction(function () use ($tier, $quantity, $email, $userId) {
                // 3. Pessimistic lock: Forces DB to lock the selected row until transaction commits
                $lockedTier = TicketTier::where('id', $tier->id)->lockForUpdate()->first();

                if ($lockedTier->remaining_capacity < $quantity) {
                    throw new ConflictHttpException("Not enough tickets remaining. Available: {$lockedTier->remaining_capacity}");
                }
                // 4. Decrement capacity
                $lockedTier->decrement('remaining_capacity', $quantity);

                // 5. Create reservation
                $reservation = Reservation::create([
                    'ticket_tier_id' => $tier->id,
                    'user_id' => $userId,
                    'guest_email' => $email,
                    'quantity' => $quantity,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(15)
                ]);
                // 6. Invalidate catalog cache so other users immediately see updated inventory
                Cache::tags(["event:{$lockedTier->event_id}"])->flush();
                return $reservation;
            });
        });
    }
}
