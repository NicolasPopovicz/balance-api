<?php

namespace App\Http\Controllers;

use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BalanceController extends Controller
{
    public function __construct(protected BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * General handler for making balances request.
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->balanceService->handleBalanceRequest($request);
    }

    /**
     * Resets the state of the balances file.
     */
    public function reset(): Response
    {
        $this->balanceService->resetBalance();
        return response(content: 'OK', status: 200);
    }
}
