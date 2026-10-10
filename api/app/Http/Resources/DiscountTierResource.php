<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DiscountTierResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'min_subtotal_cents' => $this->min_subtotal_cents,
            // Chaîne à deux décimales (ex. "3.00") : pas de flottant.
            'rate_percent' => $this->rate_percent,
            'active' => $this->active,
        ];
    }
}
