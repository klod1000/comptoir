<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreDiscountTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO séance 4 : réserver la création de paliers aux administrateurs (Policy).
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:100'],
            'min_subtotal_cents' => ['required', 'integer', 'min:1', 'unique:discount_tiers,min_subtotal_cents'],
            'rate_percent' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'lte:50'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
