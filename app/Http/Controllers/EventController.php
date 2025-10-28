<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * General handler for making events request.
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->eventService->handleEventRequest($request);
    }
}
