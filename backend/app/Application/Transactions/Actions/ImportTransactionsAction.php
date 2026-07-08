<?php

namespace App\Application\Transactions\Actions;

use App\Domain\Transactions\Contracts\ExternalTransactionsClient;
use App\Domain\Transactions\Enums\TransactionStatus;
use App\Jobs\ProcessTransactionJob;
use App\Models\ImportBatch;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportTransactionsAction
{
    public function __construct(
        private readonly ExternalTransactionsClient $client,
        private readonly SyncImportBatchStatusAction $syncImportBatchStatus,
    ) {
    }

    public function execute(string $source = 'mock', ?int $limit = null): ImportBatch
    {
        $batch = ImportBatch::query()->create([
            'source' => $source,
            'status' => 'fetching',
            'started_at' => now(),
        ]);

        try {
            $payloads = $this->client->fetch($source, $limit);

            DB::transaction(function () use ($batch, $payloads, $limit): void {
                foreach ($payloads as $payload) {
                    $transaction = Transaction::query()->create([
                        'import_batch_id' => $batch->id,
                        'external_id' => is_array($payload) && isset($payload['id']) ? (string) $payload['id'] : null,
                        'status' => TransactionStatus::Pending,
                        'payload' => is_array($payload) ? $payload : ['raw' => $payload],
                    ]);

                    ProcessTransactionJob::dispatch($transaction->id)->afterCommit();
                }

                $batch->update([
                    'total_received' => count($payloads),
                    'queued_count' => count($payloads),
                    'metadata' => ['limit' => $limit],
                ]);
            });

            $this->syncImportBatchStatus->execute($batch->id);

            return $batch->fresh();
        } catch (Throwable $exception) {
            $batch->update([
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
                'finished_at' => now(),
            ]);

            report($exception);

            throw $exception;
        }
    }
}