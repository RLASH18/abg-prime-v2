<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\UpdateDeliveryStatusRequest;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryController extends Controller
{
    /**
     * Inject Delivery Service
     *
     * @param DeliveryService $deliveryService
     */
    public function __construct(
        protected DeliveryService $deliveryService
    ) {}

    /**
     * Display a listing of deliveries
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $deliveries = $this->deliveryService->getAllPaginated(10, $filters);

        return Inertia::render('admin/Deliveries/Index', [
            'deliveries' => $deliveries,
            'filters' => $filters
        ]);
    }

    /**
     * Display the specified delivery
     */
    public function show(int $id)
    {
        $delivery = $this->deliveryService->findById($id);

        if (! $delivery) {
            return $this->flashError('Delivery not found', 'admin.deliveries.index');
        }

        return Inertia::render('admin/Deliveries/Show', [
            'delivery' => $delivery
        ]);
    }

    /**
     * Update delivery status
     */
    public function updateStatus(UpdateDeliveryStatusRequest $request, int $id)
    {
        $validated = $request->validated();
        $proofFile = $request->file('proof_of_delivery');

        $updated = $this->deliveryService->updateDeliveryStatus(
            $id,
            $validated['status'],
            $validated,
            $proofFile
        );

        if ($updated) {
            return $this->flashSuccess('Delivery updated successfully', 'admin.deliveries.index');
        }

        return $this->flashError('Delivery not found', 'admin.deliveries.index');
    }
}
