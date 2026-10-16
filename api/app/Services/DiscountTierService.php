<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DiscountTier;

final class DiscountTierService
{
    /**
     * @param  array{label: string, min_subtotal_cents: int, rate_percent: int|float|string, active?: bool}  $data
     */
    public function create(array $data): DiscountTier
    {
        $tier = DiscountTier::create($data);

        // Relit la ligne pour exposer les valeurs normalisées par la base (ex. "3.50").
        return $tier->refresh();
    }

    /**
     * Palier actif au seuil le plus élevé inférieur ou égal au sous-total.
     */
    public function applicableTier(int $subtotalCents): ?DiscountTier
    {
        return DiscountTier::query()
            ->active()
            ->where('min_subtotal_cents', '<=', $subtotalCents)
            ->orderByDesc('min_subtotal_cents')
            ->first();
    }

    /**
     * Remise en centimes, arrondie au centime le plus proche (demi vers le haut), en entiers uniquement.
     */
    public function discountCents(int $subtotalCents, DiscountTier $tier): int
    {
        // "3.00" (cast decimal:2) → 300 points de base.
        $basisPoints = (int) str_replace('.', '', $tier->rate_percent);

        return intdiv($subtotalCents * $basisPoints + 5000, 10000);
    }
}
