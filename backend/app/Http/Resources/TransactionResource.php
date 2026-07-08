<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'import_batch_id' => $this->import_batch_id,
            'external_id' => $this->external_id,
            'reference' => $this->reference,
            'description' => $this->description,
            'status' => $this->status?->value,
            'amount_cents' => $this->amount_cents,
            'amount' => $this->amount_cents !== null ? number_format($this->amount_cents / 100, 2, '.', '') : null,
            'currency' => $this->currency,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'processed_at' => $this->processed_at?->toIso8601String(),
            'error_message' => $this->error_message,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
