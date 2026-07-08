<?php

namespace App\Jobs;

use App\Application\Transactions\Actions\ProcessTransactionAction;
use App\Application\Transactions\Actions\SyncImportBatchStatusAction;
use App\Domain\Transactions\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [5, 15, 30];

    /**
     * Create a new job instance.
     */
    public function __construct(public int $transactionId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessTransactionAction $action): void
    {
        $action->execute($this->transactionId);
    }

    public function failed(Throwable $exception): void
    {
        $transaction = Transaction::query()->find($this->transactionId);

        if ($transaction === null) {
            return;
        }

        if ($transaction->status === TransactionStatus::Pending) {
            $transaction->update([
                'status' => TransactionStatus::Failed,
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
                'processed_at' => now(),
            ]);
        }

        Log::error('Transaction processing failed.', [
            'transaction_id' => $transaction->id,
            'import_batch_id' => $transaction->import_batch_id,
            'message' => $exception->getMessage(),
        ]);

        if ($transaction->import_batch_id !== null) {
            app(SyncImportBatchStatusAction::class)->execute($transaction->import_batch_id);
        }
    }
}
