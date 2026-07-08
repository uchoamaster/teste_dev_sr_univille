<?php

namespace App\Domain\Transactions\ValueObjects;

use App\Domain\Transactions\Exceptions\InvalidTransactionPayload;

final class Money
{
    private function __construct(
        private readonly int $amountInCents,
        private readonly string $currency,
    ) {
    }

    public static function fromInput(int|float|string $value, string $currency = 'BRL'): self
    {
        $normalizedCurrency = strtoupper(trim($currency));

        if (! preg_match('/^[A-Z]{3}$/', $normalizedCurrency)) {
            throw new InvalidTransactionPayload('Currency must be a valid ISO-4217 code.');
        }

        $normalized = trim((string) $value);
        $normalized = str_replace(',', '.', $normalized);

        if (! preg_match('/^-?\d+(?:\.\d{1,2})?$/', $normalized)) {
            throw new InvalidTransactionPayload('Amount must be numeric with up to 2 decimal places.');
        }

        $sign = str_starts_with($normalized, '-') ? -1 : 1;
        $normalized = ltrim($normalized, '-');
        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '0');
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');

        return new self((((int) $whole) * 100 + (int) $fraction) * $sign, $normalizedCurrency);
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function asDecimal(): string
    {
        return number_format($this->amountInCents / 100, 2, '.', '');
    }
}