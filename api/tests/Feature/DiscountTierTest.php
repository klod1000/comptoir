<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\DiscountTier;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Testing\TestResponse;

function validTierPayload(array $overrides = []): array
{
    return array_merge([
        'label' => 'Bronze',
        'min_subtotal_cents' => 50000,
        'rate_percent' => 3,
    ], $overrides);
}

function createStandardTiers(): void
{
    DiscountTier::factory()->create(['label' => 'Bronze', 'min_subtotal_cents' => 50000, 'rate_percent' => '3.00']);
    DiscountTier::factory()->create(['label' => 'Argent', 'min_subtotal_cents' => 100000, 'rate_percent' => '5.00']);
    DiscountTier::factory()->create(['label' => 'Or', 'min_subtotal_cents' => 500000, 'rate_percent' => '8.00']);
}

function placeOrderWithSubtotal(int $subtotalCents): TestResponse
{
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['price_cents' => $subtotalCents, 'stock' => 10]);

    return test()->postJson('/api/orders', [
        'customer_id' => $customer->id,
        'lines' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);
}

// Gestion des paliers

it('crée un palier et répond 201 avec le palier créé', function () {
    $this->postJson('/api/discount-tiers', validTierPayload(['rate_percent' => 3.5]))
        ->assertCreated()
        ->assertJsonPath('data.label', 'Bronze')
        ->assertJsonPath('data.min_subtotal_cents', 50000)
        ->assertJsonPath('data.rate_percent', '3.50')
        ->assertJsonPath('data.active', true)
        ->assertJsonStructure(['data' => ['id', 'label', 'min_subtotal_cents', 'rate_percent', 'active']]);

    expect(DiscountTier::count())->toBe(1);
});

it('crée un palier inactif quand active vaut false', function () {
    $this->postJson('/api/discount-tiers', validTierPayload(['active' => false]))
        ->assertCreated()
        ->assertJsonPath('data.active', false);
});

it('refuse un taux de remise invalide', function (mixed $rate) {
    $this->postJson('/api/discount-tiers', validTierPayload(['rate_percent' => $rate]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('rate_percent');
})->with([
    'nul' => [0],
    'négatif' => [-2],
    'supérieur à 50' => [50.01],
    'trois décimales' => ['2.555'],
    'absent' => [null],
    'non numérique' => ['abc'],
]);

it('accepte un taux de 50 % ou à deux décimales', function (mixed $rate) {
    $this->postJson('/api/discount-tiers', validTierPayload(['rate_percent' => $rate]))
        ->assertCreated();
})->with([
    'limite haute' => [50],
    'deux décimales' => ['2.55'],
]);

it('refuse un seuil invalide', function (mixed $threshold) {
    $this->postJson('/api/discount-tiers', validTierPayload(['min_subtotal_cents' => $threshold]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('min_subtotal_cents');
})->with([
    'zéro' => [0],
    'non entier' => [100.5],
    'absent' => [null],
]);

it('refuse un seuil déjà utilisé par un autre palier', function () {
    DiscountTier::factory()->create(['min_subtotal_cents' => 50000]);

    $this->postJson('/api/discount-tiers', validTierPayload(['min_subtotal_cents' => 50000]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('min_subtotal_cents');
});

it('refuse un libellé absent ou trop long', function (?string $label) {
    $this->postJson('/api/discount-tiers', validTierPayload(['label' => $label]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('label');
})->with([
    'absent' => [null],
    'plus de 100 caractères' => [str_repeat('a', 101)],
]);

it('liste tous les paliers triés par seuil croissant', function () {
    DiscountTier::factory()->create(['min_subtotal_cents' => 500000]);
    DiscountTier::factory()->create(['min_subtotal_cents' => 50000, 'active' => false]);
    DiscountTier::factory()->create(['min_subtotal_cents' => 100000]);

    $this->getJson('/api/discount-tiers')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.min_subtotal_cents', 50000)
        ->assertJsonPath('data.1.min_subtotal_cents', 100000)
        ->assertJsonPath('data.2.min_subtotal_cents', 500000);
});

// Application aux commandes

it('n\'applique aucune remise sous le premier palier', function () {
    createStandardTiers();

    placeOrderWithSubtotal(40000)
        ->assertCreated()
        ->assertJsonPath('data.subtotal_cents', 40000)
        ->assertJsonPath('data.discount_cents', 0)
        ->assertJsonPath('data.discount_tier_id', null)
        ->assertJsonPath('data.total_cents', 40000);
});

it('applique le palier dont le seuil est exactement atteint', function () {
    createStandardTiers();
    $bronze = DiscountTier::where('label', 'Bronze')->firstOrFail();

    placeOrderWithSubtotal(50000)
        ->assertCreated()
        ->assertJsonPath('data.discount_tier_id', $bronze->id)
        ->assertJsonPath('data.discount_cents', 1500)
        ->assertJsonPath('data.total_cents', 48500);
});

it('applique le palier le plus élevé atteint', function () {
    createStandardTiers();
    $gold = DiscountTier::where('label', 'Or')->firstOrFail();

    placeOrderWithSubtotal(600000)
        ->assertCreated()
        ->assertJsonPath('data.discount_tier_id', $gold->id)
        ->assertJsonPath('data.discount_cents', 48000)
        ->assertJsonPath('data.total_cents', 552000);
});

it('ignore un palier inactif', function () {
    createStandardTiers();
    DiscountTier::where('label', 'Or')->update(['active' => false]);
    $silver = DiscountTier::where('label', 'Argent')->firstOrFail();

    placeOrderWithSubtotal(600000)
        ->assertCreated()
        ->assertJsonPath('data.discount_tier_id', $silver->id)
        ->assertJsonPath('data.discount_cents', 30000)
        ->assertJsonPath('data.total_cents', 570000);
});

it('arrondit la remise au centime le plus proche, demi vers le haut', function () {
    createStandardTiers();

    placeOrderWithSubtotal(50150)
        ->assertCreated()
        ->assertJsonPath('data.discount_cents', 1505)
        ->assertJsonPath('data.total_cents', 48645);
});

it('enregistre le sous-total, la remise, le palier et le total en base', function () {
    createStandardTiers();
    $silver = DiscountTier::where('label', 'Argent')->firstOrFail();

    placeOrderWithSubtotal(120000)
        ->assertCreated()
        ->assertJsonPath('data.subtotal_cents', 120000)
        ->assertJsonPath('data.discount_cents', 6000)
        ->assertJsonPath('data.discount_tier_id', $silver->id)
        ->assertJsonPath('data.total_cents', 114000);

    $order = Order::firstOrFail();

    expect($order->subtotal_cents)->toBe(120000)
        ->and($order->discount_cents)->toBe(6000)
        ->and($order->discount_tier_id)->toBe($silver->id)
        ->and($order->total_cents)->toBe(114000);
});

it('conserve les commandes quand leur palier est supprimé', function () {
    createStandardTiers();

    placeOrderWithSubtotal(50000)->assertCreated();

    DiscountTier::where('label', 'Bronze')->firstOrFail()->delete();

    $order = Order::firstOrFail();

    expect($order->discount_tier_id)->toBeNull()
        ->and($order->discount_cents)->toBe(1500)
        ->and($order->total_cents)->toBe(48500);
});
