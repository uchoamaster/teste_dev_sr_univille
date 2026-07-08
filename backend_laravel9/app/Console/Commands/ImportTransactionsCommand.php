<?php

namespace App\Console\Commands;

use App\Application\Transactions\Actions\ImportTransactionsAction;
use Illuminate\Console\Command;
use Throwable;

class ImportTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:import {--source=mock : Source key configured for external transactions} {--limit= : Maximum number of records to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports transactions from the configured external source and dispatches queue jobs for processing.';

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
