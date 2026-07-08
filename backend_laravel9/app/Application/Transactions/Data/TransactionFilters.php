<?php

namespace App\Application\Transactions\Data;

use App\Domain\Transactions\ValueObjects\Money;
use Carbon\CarbonImmutable;

final class TransactionFilters
{
    private function __construct(
        public readonly ?string $status,
        public readonly ?CarbonImmutable $startDate,
        public readonly ?CarbonImmutable $endDate,
        public readonly ?int $minAmountCents,
        public readonly ?int $maxAmountCents,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['status']) ? (string) $data['status'] : null,
            isset($data['start_date']) ? CarbonImmutable::parse((string) $data['start_date'])->startOfDay() : null,
            isset($data['end_date']) ? CarbonImmutable::parse((string) $data['end_date'])->endOfDay() : null,
            isset($data['min_amount']) ? Money::fromInput((string) $data['min_amount'])->amountInCents() : null,
            isset($data['max_amount']) ? Money::fromInput((string) $data['max_amount'])->amountInCents() : null,
        );
    }
}