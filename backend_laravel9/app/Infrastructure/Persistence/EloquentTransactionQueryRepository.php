<?php

namespace App\Infrastructure\Persistence;

use App\Application\Transactions\Data\TransactionFilters;
use App\Domain\Transactions\Contracts\TransactionQueryRepository;
use App\Models\ImportBatch;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTransactionQueryRepository implements TransactionQueryRepository
{
    public function paginate(TransactionFilters $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::query()->latest('occurred_at')->latest('id');

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        if ($filters->startDate !== null) {
            $query->where('occurred_at', '>=', $filters->startDate);
        }

        if ($filters->endDate !== null) {
            $query->where('occurred_at', '<=', $filters->endDate);
        }

        if ($filters->minAmountCents !== null) {
            $query->where('amount_cents', '>=', $filters->minAmountCents);
        }

        if ($filters->maxAmountCents !== null) {
            $query->where('amount_cents', '<=', $filters->maxAmountCents);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function summary(): array
    {
        $summary = Transaction::query()
            ->selectRaw('COUNT(*) as total_transactions')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'processed' THEN 1 ELSE 0 END) as processed_count")
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count")
            ->selectRaw("SUM(CASE WHEN status = 'invalid' THEN 1 ELSE 0 END) as invalid_count")
            ->first();

        $lastImportAt = ImportBatch::query()->max('created_at');

        return [
            'pending_count' => (int) ($summary?->pending_count ?? 0),
            'processed_count' => (int) ($summary?->processed_count ?? 0),
            'failed_count' => (int) ($summary?->failed_count ?? 0),
            'invalid_count' => (int) ($summary?->invalid_count ?? 0),
            'total_transactions' => (int) ($summary?->total_transactions ?? 0),
            'last_import_at' => $lastImportAt !== null ? CarbonImmutable::parse($lastImportAt)->toIso8601String() : null,
        ];
    }
}