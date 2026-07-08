<?php

namespace App\Application\Transactions\Actions;

use App\Domain\Transactions\Data\ExternalTransactionData;
use App\Domain\Transactions\Enums\TransactionStatus;
use App\Domain\Transactions\Exceptions\InvalidTransactionPayload;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProcessTransactionAction
{
    public function __construct(
        private readonly SyncImportBatchStatusAction $syncImportBatchStatus,
    ) {
    }

    public function execute(int $transactionId): void
    {
        $transaction = Transaction::query()->find($transactionId);

        if ($transaction === null) {
            throw new ModelNotFoundException('Transaction not found.');
        }

        if ($transaction->status !== TransactionStatus::Pending) {
            return;
        }

        try {
            $rawPayload = $transaction->payload['raw'] ?? $transaction->payload;
            $data = ExternalTransactionData::fromPayload($rawPayload);

            $transaction->update([
                'external_id' => $data->externalId,
                'reference' => $data->reference,
                'description' => $data->description,
                'amount_cents' => $data->amount->amountInCents(),
                'currency' => $data->amount->currency(),
                'status' => TransactionStatus::Processed,
                'occurred_at' => $data->occurredAt,
                'processed_at' => now(),
                'error_message' => null,
            ]);
        } catch (InvalidTransactionPayload $exception) {
            $transaction->update([
                'status' => TransactionStatus::Invalid,
                'error_message' => $exception->getMessage(),
                'processed_at' => now(),
            ]);
        }

        if ($transaction->import_batch_id !== null) {
            $this->syncImportBatchStatus->execute($transaction->import_batch_id);
        }
    }
}