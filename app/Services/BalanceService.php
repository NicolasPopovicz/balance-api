<?php

namespace App\Services;

use App\Utils\Util;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceService
{
    /**
     * Retireves the total balance for given account. Returns 0 if not exists.
     * @param  Request $request
     * @return JsonResponse
     */
    public function handleBalanceRequest(Request $request): JsonResponse
    {
        $accountId = $request->query('account_id');

        if (!$accountId) {
            return response()->json(0, 404);
        }

        $balance = $this->getBalance((int) $accountId);

        return response()->json(is_null($balance) ? 0 : $balance['balance'], is_null($balance) ? 404 : 200);
    }

    /**
     * This method clear the file that keeps the data.
     */
    public function resetBalance(): void
    {
        Util::resetBalanceFile();
    }

    /**
     * This proccesses the search to the account, retriving its total balance.
     * @param  integer $accountId
     * @return array|null
     */
    private function getBalance(int $accountId): ?array
    {
        return Util::getBalanceById($accountId);
    }
}