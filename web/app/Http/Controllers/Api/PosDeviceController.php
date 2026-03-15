<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rfid\RfidScanRequest;
use App\Services\ItemService;
use Illuminate\Http\JsonResponse;

class PosDeviceController extends Controller
{
    /**
     * Inject Item Service.
     *
     * @param ItemService $itemService
     */
    public function __construct(
        protected ItemService $itemService,
    ) {}

    /**
     * Look up item info by item_code (read-only, no stock change).
     */
    public function lookup(string $code): JsonResponse
    {
        $item = $this->itemService->findByCode($code);

        if (! $item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ], 200);
    }

    /**
     * Handle RFID scan and adjust item quantity.
     */
    public function scan(RfidScanRequest $request): JsonResponse
    {
        $result = $this->itemService->adjustQuantityByCode(
            $request->validated()['item_code'],
            $request->validated()['action'],
            $request->validated()['quantity']
        );

        if (! $result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => $result['item'],
        ], 200);
    }
}
