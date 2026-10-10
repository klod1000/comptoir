<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class DiscountTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'min_subtotal_cents',
        'rate_percent',
        'active',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected function casts(): array
    {
        return [
            'min_subtotal_cents' => 'integer',
            'rate_percent' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @param  Builder<DiscountTier>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }
}
