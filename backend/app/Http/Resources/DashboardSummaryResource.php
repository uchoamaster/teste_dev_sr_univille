<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pending_count' => $this->resource['pending_count'] ?? 0,
            'processed_count' => $this->resource['processed_count'] ?? 0,
            'failed_count' => $this->resource['failed_count'] ?? 0,
            'invalid_count' => $this->resource['invalid_count'] ?? 0,
            'total_transactions' => $this->resource['total_transactions'] ?? 0,
            'last_import_at' => $this->resource['last_import_at'] ?? null,
        ];
    }
}
