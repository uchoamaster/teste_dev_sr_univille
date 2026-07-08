<?php

namespace App\Providers;

use App\Domain\Transactions\Contracts\ExternalTransactionsClient;
use App\Domain\Transactions\Contracts\TransactionQueryRepository;
use App\Infrastructure\Persistence\EloquentTransactionQueryRepository;
use App\Infrastructure\Transactions\LocalJsonExternalTransactionsClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExternalTransactionsClient::class, LocalJsonExternalTransactionsClient::class);
        $this->app->bind(TransactionQueryRepository::class, EloquentTransactionQueryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
