<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Services\ItemForecastService;
use App\Services\ItemService;
use Illuminate\Http\JsonResponse;

class ItemForecastController extends Controller
{
    /**
     * Inject required services
     *
     * @param ItemService $itemService
     * @param ItemForecastService $forecastService
     */
    public function __construct(
        protected ItemService $itemService,
        protected ItemForecastService $forecastService
    ) {}

    /**
     * Generate an AI forecast report for a specific item.
     */
    public function generate(int $item): JsonResponse
    {
        $inventoryItem = $this->itemService->find($item);

        if (! $inventoryItem) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        $report = $this->forecastService->generate($inventoryItem);

        return response()->json([
            'report' => $report
        ]);
    }
}
