<?php

namespace App\Services;

use App\Enums\EventType;
use App\Utils\Util;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventService
{
    /**
     * @param  Request $request
     * @return JsonResponse
     */
    public function handleEventRequest(Request $request): JsonResponse
    {
        $eventType = $request->post('type');

        if (!$eventType) {
            return response()->json(0, 404);
        }

        return match ($eventType) {
            EventType::DEPOSIT->value  => $this->processDeposit($request),
            EventType::WITHDRAW->value => $this->processWithdraw($request),
            EventType::TRANSFER->value => $this->processTransfer($request)
        };
    }

    /**
     * Make a deposit to desired account.
     * If account doesn't exists, it creates the register.
     * @param  Request $request
     * @return JsonResponse
     */
    private function processDeposit(Request $request): JsonResponse
    {
        $destination = $request->post('destination');
        $amount      = (int) $request->post('amount');

        $balance = Util::getBalanceById($destination);

        if (!$balance) {
            Util::createBalance([
                $destination => ['balance' => $amount],
            ]);

            return response()->json([
                'destination' => [
                    'id'      => $destination,
                    'balance' => $amount
                ]
            ], 201);
        }

        $newBalance = Util::updateBalance($destination, [
            'balance' => $balance['balance'] + $amount
        ]);

        return response()->json([
            'destination' => [
                'id'      => $destination,
                'balance' => $newBalance['balance']
            ]
        ], 201);
    }

    /**
     * @param  Request $request
     * @return JsonResponse
     */
    private function processWithdraw(Request $request): JsonResponse
    {
        $origin = $request->post('origin');
        $amount = (int) $request->post('amount');

        $balance = Util::getBalanceById($origin);

        if (!$balance) {
            return response()->json(0, 404);
        }

        $transfer = Util::updateBalance($origin, [
            'balance' => $balance['balance'] - $amount
        ]);

        return response()->json([
            'origin' => [
                'id'      => $origin,
                'balance' => $transfer['balance']
            ]
        ], 201);
    }

    /**
     * This function proccess a balance transfer to another account.
     * If the origin account doesn't exist, returns 404.
     * @param  Request $request
     * @return JsonResponse
     */
    private function processTransfer(Request $request): JsonResponse
    {
        $origin      = $request->post('origin');
        $destination = $request->post('destination');
        $amount      = (int) $request->post('amount');

        $originInitalBalance       = Util::getBalanceById($origin);
        $destinationInitialBalance = Util::getBalanceById($destination);

        if (!$destinationInitialBalance) {
            Util::createBalance([
                $destination => ['balance' => 0],
            ]);

            // Rewrite the value for the destination from initial reading.
            $destinationInitialBalance = Util::getBalanceById($destination);
        }

        if (!$originInitalBalance) {
            return response()->json(0, 404);
        }

        $originBalance = Util::updateBalance($originInitalBalance['id'], [
            'balance' => $originInitalBalance['balance'] - $amount
        ]);

        $destinationBalance = Util::updateBalance($destinationInitialBalance['id'], [
            'balance' => $destinationInitialBalance['balance'] + $amount
        ]);

        return response()->json([
            'origin'      => ['id' => $origin,      'balance' => $originBalance['balance']],
            'destination' => ['id' => $destination, 'balance' => $destinationBalance['balance']]
        ], 201);
    }
}