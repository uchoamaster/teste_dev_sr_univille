<?php

namespace App\Http\Requests;

use App\Application\Transactions\Data\TransactionFilters;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTransactionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(['pending', 'processed', 'failed', 'invalid'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'gte:min_amount'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function filters(): TransactionFilters
    {
        return TransactionFilters::fromArray($this->validated());
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 15);
    }
}
