<?php

namespace App\Application\Transactions\Actions;

use App\Models\ImportBatch;
use App\Models\Transaction;

class SyncImportBatchStatusAction
{
    public function execute(int $batchId): void
    {
        $batch = ImportBatch::query()->find($batchId);

        if ($batch === null) {
            return;
        }

        $summary = Transaction::query()
            ->where('import_batch_id', $batchId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'processed' THEN 1 ELSE 0 END) as processed_count")
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count")
            ->selectRaw("SUM(CASE WHEN status = 'invalid' THEN 1 ELSE 0 END) as invalid_count")
            ->first();

        $pendingCount = (int) ($summary?->pending_count ?? 0);
        $processedCount = (int) ($summary?->processed_count ?? 0);
        $failedCount = (int) ($summary?->failed_count ?? 0);
        $invalidCount = (int) ($summary?->invalid_count ?? 0);
        $totalCount = (int) ($summary?->total ?? 0);

        $status = match (true) {
            $totalCount === 0 => 'completed',
            $pendingCount > 0 => 'processing',
            ($failedCount + $invalidCount) > 0 => 'completed_with_errors',
            default => 'completed',
        };

        $batch->update([
            'status' => $status,
            'total_received' => max($batch->total_received, $totalCount),
            'queued_count' => $totalCount,
            'processed_count' => $processedCount,
            'failed_count' => $failedCount,
            'invalid_count' => $invalidCount,
            'finished_at' => $pendingCount === 0 ? now() : null,
        ]);
    }
}