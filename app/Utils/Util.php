<?php

namespace App\Utils;

use Illuminate\Support\Facades\File;

class Util
{
    private const BALANCE_FILENAME = 'balance-data.json';

    /**
     * Retrieves a balance entry by its identifier.
     * @param  string|int $identifier
     * @return array|null Array with keys 'id' and 'balance', or null when not found.
     */
    public static function getBalanceById($identifier): ?array
    {
        $balances = self::readBalances();
        $key = (string) $identifier;

        if (!array_key_exists($key, $balances)) {
            return null;
        }

        $entry = $balances[$key];

        if (is_array($entry)) {
            return [
                'id'      => $key,
                'balance' => $entry['balance'] ?? 0,
            ];
        }

        return [
            'id'      => $key,
            'balance' => $entry,
        ];
    }

    /**
     * Creates the balance file (or overwrites an existing one) with the provided data.
     * @param  array $balances Array keyed by balance identifier. Each item should be an array of balance attributes.
     * @return array The data persisted to disk after normalization.
     */
    public static function createBalance(array $balances = []): array
    {
        $normalized = self::normalizeBalances($balances);
        self::persistBalances($normalized);

        return $normalized;
    }

    /**
     * Updates (or creates) a single balance entry inside the JSON file.
     * @param  string|int $identifier Balance identifier that will be used as the JSON key.
     * @param  array $payload Data to store for the identifier (e.g. ['balance' => 20]).
     * @return array The data persisted for the given identifier.
     */
    public static function updateBalance($identifier, array $payload): array
    {
        $balances = self::readBalances();
        $key = (string) $identifier;
        $balances[$key] = $payload;

        self::persistBalances($balances);

        return $balances[$key];
    }

    /**
     * Clears all stored balance information.
     * @return void
     */
    public static function resetBalanceFile(): void
    {
        self::persistBalances([]);
    }

    /**
     * Returns the currently stored balance data.
     * @return array
     */
    public static function readBalances(): array
    {
        $path = self::balanceFilePath();

        if (!File::exists($path)) {
            return self::createBalance();
        }

        $contents = File::get($path);

        if (trim($contents) === '') {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Ensures the balances array is keyed by identifier and stored as arrays.
     * @param  array $balances
     * @return array
     */
    private static function normalizeBalances(array $balances): array
    {
        $normalized = [];

        foreach ($balances as $identifier => $data) {
            $key = is_array($data) && array_key_exists('id', $data)
                ? (string) $data['id']
                : (string) $identifier;

            $normalized[$key] = is_array($data) ? $data : ['balance' => $data];
        }

        return $normalized;
    }

    /**
     * Persists the provided balance data into the JSON file.
     * @param array $balances
     */
    private static function persistBalances(array $balances): void
    {
        $path = self::balanceFilePath();

        File::ensureDirectoryExists(dirname($path));
        File::put(
            $path,
            json_encode($balances, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Returns the full path to the balance JSON file.
     * @return string
     */
    private static function balanceFilePath(): string
    {
        return storage_path('app/' . self::BALANCE_FILENAME);
    }
}
