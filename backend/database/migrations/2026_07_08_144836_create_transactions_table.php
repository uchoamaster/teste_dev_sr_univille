<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_id')->nullable()->index();
            $table->string('reference')->nullable()->index();
            $table->text('description')->nullable();
            $table->bigInteger('amount_cents')->nullable()->index();
            $table->string('currency', 3)->nullable();
            $table->string('status')->index();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->timestamp('processed_at')->nullable();
            $table->json('payload');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
