<?php

namespace App\Domain\Transactions\Contracts;

interface ExternalTransactionsClient
{
    /**
     * @return array<int, mixed>
     */
    public function fetch(string $source, ?int $limit = null): array;
}