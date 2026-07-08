<?php

namespace Tests\Feature;

use App\Models\ImportBatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_endpoint_processes_transactions_and_updates_summary(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions/import', [
            'source' => 'mock',
        ]);

        $response
            ->assertAccepted()
            ->assertJsonPath('batch.status', 'completed_with_errors')
            ->assertJsonPath('batch.queued_count', 5);

        $this->getJson('/api/dashboard/summary')
            ->assertOk()
            ->assertJsonPath('pending_count', 0)
            ->assertJsonPath('processed_count', 3)
            ->assertJsonPath('invalid_count', 2)
            ->assertJsonPath('total_transactions', 5);

        $this->getJson('/api/transactions?status=invalid')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/transactions?min_amount=900')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_import_endpoint_returns_service_unavailable_when_source_cannot_be_read(): void
    {
        Sanctum::actingAs(User::factory()->create());

        config()->set('services.transactions.sources.mock', base_path('storage/app/missing-transactions.json'));

        $this->postJson('/api/transactions/import', [
            'source' => 'mock',
        ])
            ->assertStatus(503)
            ->assertJsonPath('message', 'The external transaction source is currently unavailable.');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('import_batches', [
            'source' => 'mock',
            'status' => 'failed',
        ]);
    }
}