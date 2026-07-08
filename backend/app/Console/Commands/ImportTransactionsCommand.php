<?php

namespace App\Console\Commands;

use App\Application\Transactions\Actions\ImportTransactionsAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('transactions:import {--source=mock : Source key configured for external transactions} {--limit= : Maximum number of records to import}')]
#[Description('Imports transactions from the configured external source and dispatches queue jobs for processing.')]
class ImportTransactionsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ImportTransactionsAction $action): int
    {
        try {
            $batch = $action->execute(
                (string) $this->option('source'),
                $this->option('limit') !== null ? (int) $this->option('limit') : null,
            );

            $this->info(sprintf(
                'Import batch #%d queued %d transaction(s) from source [%s].',
                $batch->id,
                $batch->queued_count,
                $batch->source,
            ));

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Unable to import transactions: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
