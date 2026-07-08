<?php

namespace App\Models;

use App\Domain\Transactions\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_batch_id',
        'external_id',
        'reference',
        'description',
        'amount_cents',
        'currency',
        'status',
        'occurred_at',
        'processed_at',
        'payload',
        'error_message',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'processed_at' => 'datetime',
        'status' => TransactionStatus::class,
    ];

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }
}
