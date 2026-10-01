<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    public function index(): JsonResponse
    {
        //catch event list for 15 minutes
        $events = Cache::tags(['events'])->remember('events:active_list', now()->addMinutes(15), function () {
            return Event::published()->with('tiers')->get();
        });

        return response()->json(EventResource::collection($events));
    }

    public function show(Event $event): JsonResponse
    {
        $eventData = Cache::tags(['events', "event:{$event->id}"])->remember(
            "event:slug:{$event->slug}",
            now()->addHours(2),
            function () use ($event) {
                return $event->load('tiers');
            }
        );

        return response()->json(new EventResource($eventData));
    }
}
