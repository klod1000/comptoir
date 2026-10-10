<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountTierRequest;
use App\Http\Resources\DiscountTierResource;
use App\Models\DiscountTier;
use App\Services\DiscountTierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class DiscountTierController extends Controller
{
    public function __construct(private readonly DiscountTierService $service) {}

    /**
     * GET /api/discount-tiers
     * Tous les paliers, sans pagination : table de configuration de petite taille.
     */
    public function index(): AnonymousResourceCollection
    {
        $tiers = DiscountTier::query()
            ->orderBy('min_subtotal_cents')
            ->get();

        return DiscountTierResource::collection($tiers);
    }

    /**
     * POST /api/discount-tiers
     */
    public function store(StoreDiscountTierRequest $request): JsonResponse
    {
        $tier = $this->service->create($request->validated());

        return DiscountTierResource::make($tier)
            ->response()
            ->setStatusCode(201);
    }
}
