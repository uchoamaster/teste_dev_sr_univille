<?php

namespace App\Providers;

use App\Domain\Transactions\Contracts\ExternalTransactionsClient;
use App\Domain\Transactions\Contracts\TransactionQueryRepository;
use App\Infrastructure\Persistence\EloquentTransactionQueryRepository;
use App\Infrastructure\Transactions\LocalJsonExternalTransactionsClient;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ExternalTransactionsClient::class, LocalJsonExternalTransactionsClient::class);
        $this->app->bind(TransactionQueryRepository::class, EloquentTransactionQueryRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::ignoreMigrations();
    }
}
