<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'source',
    'status',
    'total_received',
    'queued_count',
    'processed_count',
    'failed_count',
    'invalid_count',
    'started_at',
    'finished_at',
    'metadata',
    'error_message',
])]
class ImportBatch extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'total_received' => 'integer',
            'queued_count' => 'integer',
            'processed_count' => 'integer',
            'failed_count' => 'integer',
            'invalid_count' => 'integer',
            'metadata' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
