<?php

namespace App\Infrastructure\Transactions;

use App\Domain\Transactions\Contracts\ExternalTransactionsClient;
use App\Domain\Transactions\Exceptions\ExternalTransactionsUnavailable;
use RuntimeException;

class LocalJsonExternalTransactionsClient implements ExternalTransactionsClient
{
    public function fetch(string $source, ?int $limit = null): array
    {
        $path = config("services.transactions.sources.{$source}");

        if (! is_string($path) || $path === '') {
            throw new RuntimeException("Unsupported transaction source [{$source}].");
        }

        if (! is_file($path) || ! is_readable($path)) {
            throw new ExternalTransactionsUnavailable("Transaction source [{$source}] is unavailable.");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new ExternalTransactionsUnavailable("Unable to read the transaction source [{$source}].");
        }

        $decoded = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw new ExternalTransactionsUnavailable("Transaction source [{$source}] contains invalid JSON.");
        }

        return $limit !== null ? array_slice($decoded, 0, $limit) : $decoded;
    }
}