<?php

namespace App\Domain\Transactions\Data;

use App\Domain\Transactions\Exceptions\InvalidTransactionPayload;
use App\Domain\Transactions\ValueObjects\Money;
use Carbon\CarbonImmutable;
use Throwable;

final class ExternalTransactionData
{
    private function __construct(
        public readonly string $externalId,
        public readonly string $reference,
        public readonly ?string $description,
        public readonly Money $amount,
        public readonly CarbonImmutable $occurredAt,
    ) {
    }

    public static function fromPayload(mixed $payload): self
    {
        if (! is_array($payload)) {
            throw new InvalidTransactionPayload('Payload must be a JSON object.');
        }

        $externalId = $payload['id'] ?? null;
        $amount = $payload['amount'] ?? null;
        $occurredAt = $payload['occurred_at'] ?? $payload['created_at'] ?? null;

        if ($externalId === null || $externalId === '') {
            throw new InvalidTransactionPayload('Transaction id is required.');
        }

        if ($amount === null || $amount === '') {
            throw new InvalidTransactionPayload('Transaction amount is required.');
        }

        if ($occurredAt === null || $occurredAt === '') {
            throw new InvalidTransactionPayload('Transaction occurrence date is required.');
        }

        try {
            $occurredAtValue = CarbonImmutable::parse((string) $occurredAt);
        } catch (Throwable) {
            throw new InvalidTransactionPayload('Transaction occurrence date is invalid.');
        }

        return new self(
            (string) $externalId,
            (string) ($payload['reference'] ?? $payload['id']),
            isset($payload['description']) ? (string) $payload['description'] : null,
            Money::fromInput($amount, (string) ($payload['currency'] ?? 'BRL')),
            $occurredAtValue,
        );
    }
}