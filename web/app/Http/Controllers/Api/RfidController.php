<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ir\IrAlertRequest;
use App\Http\Requests\Rfid\RfidScanRequest;
use App\Services\IrAlertService;
use App\Services\ItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    /**
     * Inject Item Service and IR Alert Service.
     *
     * @param ItemService    $itemService
     * @param IrAlertService $irAlertService
     */
    public function __construct(
        protected ItemService    $itemService,
        protected IrAlertService $irAlertService,
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

    /**
     * Log an IR sensor alert from the POS machine.
     *
     * @param IrAlertRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logIrAlert(IrAlertRequest $request): JsonResponse
    {
        $alert = $this->irAlertService->logAlert(
            $request->validated()['item_code'] ?? null,
            $request->validated()['alert_type'],
            $request->validated()['notes'] ?? null,
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'IR alert logged',
            'data'    => $alert,
        ], 201);
    }
}
