<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportTransactionsRequest extends FormRequest
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
            'source' => ['nullable', 'string', Rule::in(['mock'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function source(): string
    {
        return (string) ($this->validated('source') ?? 'mock');
    }

    public function limit(): ?int
    {
        $value = $this->validated('limit');

        return $value !== null ? (int) $value : null;
    }
}
