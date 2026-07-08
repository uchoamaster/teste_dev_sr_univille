<?php

namespace App\Domain\Transactions\Contracts;

use App\Application\Transactions\Data\TransactionFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransactionQueryRepository
{
    public function paginate(TransactionFilters $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return array<string, int|string|null>
     */
    public function summary(): array;
}